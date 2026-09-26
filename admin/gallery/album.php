<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/_photo-uploader.php';
$adminUser = admin_require_permission('content.manage');
$pdo = admin_require_db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM gallery_albums WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$album = $stmt->fetch();
if (!$album) { admin_flash('error', 'Gallery album not found.'); header('Location: ' . admin_url('gallery/')); exit; }

function admin_gallery_delete_photo_file_if_unused(PDO $pdo, string $path, int $deletedPhotoId): void
{
    $path = trim($path);
    if ($path === '' || preg_match('#^[a-z][a-z0-9+.-]*://#i', $path)) {
        return;
    }

    $remaining = $pdo->prepare('SELECT COUNT(*) FROM gallery_photos WHERE image_path = ? AND id <> ?');
    $remaining->execute([$path, $deletedPhotoId]);
    if ((int) $remaining->fetchColumn() > 0) {
        return;
    }

    $relativePath = ltrim(str_replace('\\', '/', $path), '/');
    if ($relativePath === '' || str_contains($relativePath, '..') || !str_starts_with($relativePath, 'uploads/gallery/')) {
        return;
    }

    $root = realpath(dirname(__DIR__, 2));
    if (!$root) {
        return;
    }

    $target = realpath($root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
    if (!$target || !is_file($target)) {
        return;
    }

    $galleryUploadRoot = realpath($root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'gallery');
    if (!$galleryUploadRoot) {
        return;
    }

    $galleryPrefix = rtrim($galleryUploadRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (!str_starts_with($target, $galleryPrefix)) {
        return;
    }

    @unlink($target);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'upload') {
        $existingCount = admin_gallery_photo_count($pdo, $id);
        [$uploaded, $uploadErrors, $album['cover_image']] = admin_gallery_store_pending_photos($pdo, $id, (string) $album['cover_image'], $existingCount);
        foreach ($uploadErrors as $uploadError) { admin_flash('error', $uploadError); }
        if ($uploaded > 0) { admin_log('uploaded', 'gallery_album', $id, $uploaded . ' gallery photo(s) uploaded.'); admin_flash('success', $uploaded . ' photo(s) uploaded.'); }
        if ($uploaded === 0 && !$uploadErrors) { admin_flash('error', 'Please add at least one photo.'); }
    } elseif ($action === 'caption') {
        foreach ($_POST['captions_existing'] ?? array() as $photoId => $caption) {
            $pdo->prepare('UPDATE gallery_photos SET caption = ? WHERE id = ? AND album_id = ?')->execute([trim((string) $caption), (int) $photoId, $id]);
        }
        admin_log('edited', 'gallery_album', $id, 'Gallery captions updated.');
        admin_flash('success', 'Captions updated.');

    } elseif ($action === 'remove') {
        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $stmt = $pdo->prepare('SELECT id, image_path FROM gallery_photos WHERE id = ? AND album_id = ? LIMIT 1');
        $stmt->execute([$photoId, $id]);
        $photo = $stmt->fetch();
        if (!$photo) {
            admin_flash('error', 'Photo not found for this album.');
        } else {
            $removedPath = (string) $photo['image_path'];
            $pdo->beginTransaction();
            try {
                $pdo->prepare('DELETE FROM gallery_photos WHERE id = ? AND album_id = ?')->execute([$photoId, $id]);
                $nextCover = $pdo->prepare('SELECT image_path FROM gallery_photos WHERE album_id = ? ORDER BY sort_order, id LIMIT 1');
                $nextCover->execute([$id]);
                $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute([(string) ($nextCover->fetchColumn() ?: ''), $id]);
                $pdo->commit();
                admin_gallery_delete_photo_file_if_unused($pdo, $removedPath, $photoId);
                admin_log('removed', 'gallery_photo', $photoId, 'Gallery photo removed.');
                admin_flash('success', 'Photo removed.');
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                admin_flash('error', 'Could not remove the photo. Please try again.');
            }
        }
    }
    header('Location: ' . admin_url('gallery/album.php?id=' . $id));
    exit;
}
$photosStmt = $pdo->prepare('SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order, id');
$photosStmt->execute([$id]);
$photos = $photosStmt->fetchAll();
$photoCount = count($photos);
$availableSlots = max(0, GALLERY_MAX_PHOTOS - $photoCount);
$adminTitle = 'Album: ' . $album['title'];
$activeNav = 'gallery';
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel" style="margin-bottom:22px">
    <div class="admin-toolbar">
        <div>
            <h2><?php echo e($album['title']); ?></h2>
            <p class="admin-muted"><?php echo e($album['category']); ?><?php echo $album['location'] ? ' | ' . e($album['location']) : ''; ?></p>
        </div>
        <a class="admin-button admin-button--light" href="<?php echo admin_url('gallery/'); ?>">Back to Gallery</a>
    </div>
    <form class="admin-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
        <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
        <input type="hidden" name="action" value="upload">
        <?php admin_gallery_render_photo_uploader((int) $photoCount, true); ?>
        <button class="admin-button" type="submit" <?php echo $availableSlots < 1 ? 'disabled' : ''; ?>>Upload Photos</button>
    </form>
</section>
<section class="admin-panel">
    <h2>Existing Uploaded Photos</h2>
    <?php if ($photos) : ?>
        <form method="post" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
            <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
            <input type="hidden" name="action" value="caption">
            <div class="admin-photo-grid">
                <?php foreach ($photos as $photo) : ?>
                    <article class="admin-photo-card">
                        <img src="<?php echo site_url($photo['image_path']); ?>" alt="">
                        <div>
                            <input class="admin-input" name="captions_existing[<?php echo (int) $photo['id']; ?>]" value="<?php echo e($photo['caption']); ?>" placeholder="Caption">
                            <div class="admin-row-actions" style="margin-top:8px">
                                <button class="admin-row-delete-button" name="action" value="remove" onclick="this.form.photo_id.value='<?php echo (int) $photo['id']; ?>'; return confirm('Are you sure you want to remove this photo?');">Remove Photo</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="photo_id" value="">
            <button class="admin-button" type="submit">Save Captions</button>
        </form>
    <?php else : ?>
        <p class="admin-muted">No photos uploaded yet. Upload at least 1 gallery photo for this album.</p>
    <?php endif; ?>
</section>
<?php admin_gallery_photo_uploader_script(); ?>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
