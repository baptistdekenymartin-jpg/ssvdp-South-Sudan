<?php
if (!defined('GALLERY_MAX_PHOTOS')) {
    define('GALLERY_MAX_PHOTOS', 6);
}

function admin_gallery_pending_files(): array
{
    if (empty($_FILES['gallery_images']) || !is_array($_FILES['gallery_images']['name'] ?? null)) {
        return array();
    }
    $files = array();
    foreach ($_FILES['gallery_images']['name'] as $i => $name) {
        if ((string) $name === '') { continue; }
        $files[] = array(
            'name' => $name,
            'type' => $_FILES['gallery_images']['type'][$i] ?? '',
            'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i] ?? '',
            'error' => $_FILES['gallery_images']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size' => $_FILES['gallery_images']['size'][$i] ?? 0,
            'source_index' => $i
        );
    }
    return $files;
}

function admin_gallery_photo_count(PDO $pdo, int $albumId): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM gallery_photos WHERE album_id = ?');
    $stmt->execute([$albumId]);
    return (int) $stmt->fetchColumn();
}

function admin_gallery_validate_pending_photos(int $existingCount = 0, bool $requirePhoto = false): string
{
    $pendingCount = count(admin_gallery_pending_files());
    if ($requirePhoto && $existingCount < 1 && $pendingCount < 1) {
        return 'Please add at least one photo.';
    }
    if ($pendingCount > max(0, GALLERY_MAX_PHOTOS - $existingCount)) {
        return 'You can upload a maximum of 6 photos per gallery album.';
    }
    return '';
}

function admin_gallery_validate_pending_file_uploads(): array
{
    $errors = array();
    foreach (admin_gallery_pending_files() as $file) {
        try {
            admin_validate_upload($file);
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
    return $errors;
}
function admin_gallery_store_pending_photos(PDO $pdo, int $albumId, string $coverImage = '', int $existingCount = 0): array
{
    $files = admin_gallery_pending_files();
    $availableSlots = max(0, GALLERY_MAX_PHOTOS - $existingCount);
    $errors = array();
    $uploaded = 0;

    if (count($files) > $availableSlots) {
        return array(0, array('You can upload a maximum of 6 photos per gallery album.'), $coverImage);
    }

    $nextSort = $existingCount;
    foreach ($files as $file) {
        try {
            $path = admin_store_upload($file, 'gallery');
            $pdo->prepare('INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) VALUES (?, ?, ?, ?)')->execute([$albumId, $path, '', $nextSort]);
            if ($coverImage === '') {
                $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute([$path, $albumId]);
                $coverImage = $path;
            }
            $uploaded++;
            $nextSort++;
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }

    return array($uploaded, $errors, $coverImage);
}

function admin_gallery_render_photo_uploader(int $existingCount = 0, bool $requirePhoto = false): void
{
    $availableSlots = max(0, GALLERY_MAX_PHOTOS - $existingCount);
    ?>
    <div class="admin-field" data-gallery-upload-form data-existing-count="<?php echo (int) $existingCount; ?>" data-max-photos="<?php echo GALLERY_MAX_PHOTOS; ?>" data-require-photo="<?php echo $requirePhoto ? '1' : '0'; ?>">
        <label>Gallery Photos</label>
        <input type="file" name="gallery_images[]" accept="image/*" multiple class="admin-input admin-gallery-file-input" data-gallery-image-input <?php echo $availableSlots < 1 ? 'disabled' : ''; ?>>
        <small>Add up to 6 photos. You can add them one at a time.</small>
        <button class="admin-button admin-button--light" type="button" data-gallery-add-photos <?php echo $availableSlots < 1 ? 'disabled' : ''; ?>>+ Add Photos</button>
        <small data-gallery-counter><?php echo (int) $existingCount; ?> of 6 photos selected<?php echo $availableSlots < 1 ? '. Remove a photo before adding another.' : ''; ?></small>
        <small class="admin-field-error" data-gallery-upload-error hidden>You can upload a maximum of 6 photos per gallery album.</small>
        <div class="admin-selected-images admin-gallery-selected-grid" data-gallery-preview-grid hidden></div>
    </div>
    <?php
}

function admin_gallery_photo_uploader_script(): void
{
    static $rendered = false;
    if ($rendered) { return; }
    $rendered = true;
    ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-gallery-upload-form]').forEach(function (form) {
        var input = form.querySelector('[data-gallery-image-input]');
        var addButton = form.querySelector('[data-gallery-add-photos]');
        var counter = form.querySelector('[data-gallery-counter]');
        var error = form.querySelector('[data-gallery-upload-error]');
        var previewGrid = form.querySelector('[data-gallery-preview-grid]');
        var existingCount = parseInt(form.getAttribute('data-existing-count') || '0', 10);
        var maxPhotos = parseInt(form.getAttribute('data-max-photos') || '6', 10);
        var requirePhoto = form.getAttribute('data-require-photo') === '1';
        var selectedFiles = [];

        function availableSlots() { return Math.max(0, maxPhotos - existingCount - selectedFiles.length); }
        function selectedTotal() { return existingCount + selectedFiles.length; }
        function setError(message) { if (error) { error.textContent = message; error.hidden = message === ''; } }
        function syncFileInput() {
            if (!input || typeof DataTransfer === 'undefined') { return; }
            var transfer = new DataTransfer();
            selectedFiles.forEach(function (file) { transfer.items.add(file); });
            input.files = transfer.files;
        }
        function updateControls() {
            var full = availableSlots() < 1;
            if (addButton) { addButton.disabled = full; }
            if (input) { input.disabled = existingCount >= maxPhotos && selectedFiles.length === 0; }
        }
        function renderPreviews() {
            if (!previewGrid || !counter) { return; }
            previewGrid.innerHTML = '';
            selectedFiles.forEach(function (file, index) {
                var card = document.createElement('div');
                card.className = 'admin-selected-image';
                var image = document.createElement('img');
                image.alt = file.name;
                image.src = URL.createObjectURL(file);
                image.addEventListener('load', function () { URL.revokeObjectURL(image.src); }, { once: true });
                var name = document.createElement('span');
                name.textContent = file.name;
                var remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = 'Remove';
                remove.addEventListener('click', function () {
                    selectedFiles.splice(index, 1);
                    syncFileInput();
                    renderPreviews();
                });
                card.appendChild(image);
                card.appendChild(name);
                card.appendChild(remove);
                previewGrid.appendChild(card);
            });
            previewGrid.hidden = selectedFiles.length === 0;
            counter.textContent = selectedTotal() + ' of ' + maxPhotos + ' photos selected' + (availableSlots() < 1 ? '. Remove a photo before adding another.' : '');
            updateControls();
            setError('');
        }
        function openPicker() {
            if (!input || input.disabled) { return; }
            input.value = '';
            input.click();
        }

        if (addButton) { addButton.addEventListener('click', openPicker); }
        if (input) {
            input.addEventListener('click', function () { input.value = ''; });
            input.addEventListener('change', function () {
                var files = Array.from(input.files || []);
                if (!files.length) { syncFileInput(); return; }
                if (files.length > availableSlots()) {
                    syncFileInput();
                    renderPreviews();
                    setError('You can upload a maximum of 6 photos per gallery album.');
                    return;
                }
                selectedFiles = selectedFiles.concat(files);
                syncFileInput();
                renderPreviews();
            });
        }
        var ownerForm = form.closest('form');
        if (ownerForm) {
            ownerForm.addEventListener('submit', function (event) {
                syncFileInput();
                if (requirePhoto && selectedFiles.length < 1) {
                    event.preventDefault();
                    setError('Please add at least one photo.');
                    return;
                }
                if (selectedFiles.length > Math.max(0, maxPhotos - existingCount)) {
                    event.preventDefault();
                    setError('You can upload a maximum of 6 photos per gallery album.');
                }
            });
        }
        renderPreviews();
    });
});
</script>
    <?php
}
