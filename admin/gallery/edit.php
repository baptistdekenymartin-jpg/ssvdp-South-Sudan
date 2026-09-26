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
if (!admin_can_edit_content($album)) { admin_forbidden(); }
$errors = array();
$galleryPhotoCount = admin_gallery_photo_count($pdo, $id);
$titleOptions = array_values(admin_gallery_album_titles());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $album['title'] = admin_resolve_select_other_post('title', $titleOptions);
    $album['category'] = admin_resolve_select_other_post('category', admin_content_programmes());
    $album['activity_date'] = trim((string) ($_POST['activity_date'] ?? ''));
    $album['location'] = admin_resolve_select_other_post('location', admin_content_locations());
    $album['description'] = trim((string) ($_POST['description'] ?? ''));
    $action = (string) ($_POST['submit_action'] ?? 'draft');
    $album['status'] = admin_content_status_for_action($action, $album);
    if (!admin_can_transition_content($action, $album)) { $errors[] = 'You do not have permission to perform that workflow action.'; }
    foreach (array('title'=>'Album Title','category'=>'Programme / Category','location'=>'Location') as $field => $label) {
        $error = admin_select_other_required_error($field, $label, $field === 'title' ? $titleOptions : ($field === 'category' ? admin_content_programmes() : admin_content_locations()));
        if ($error !== '') { $errors[] = $error; }
    }
    $pendingGalleryError = admin_gallery_validate_pending_photos($galleryPhotoCount);
    if ($pendingGalleryError !== '') { $errors[] = $pendingGalleryError; }
    if (!$errors) {
        $slug = admin_slug_for_save($pdo, 'gallery_albums', $album['title'], $album);
        $stmt = $pdo->prepare('UPDATE gallery_albums SET title=?, slug=?, category=?, activity_date=?, location=?, description=?, status=? WHERE id=?');
        $stmt->execute([$album['title'], $slug, $album['category'], $album['activity_date'] ?: null, $album['location'], $album['description'], $album['status'], $id]);
        [$uploadedGalleryPhotos, $galleryUploadErrors, $album['cover_image']] = admin_gallery_store_pending_photos($pdo, $id, (string) $album['cover_image'], $galleryPhotoCount);
        foreach ($galleryUploadErrors as $galleryUploadError) { admin_flash('error', $galleryUploadError); }
        if ($uploadedGalleryPhotos > 0) { admin_log('uploaded', 'gallery_album', $id, $uploadedGalleryPhotos . ' gallery photo(s) uploaded.'); }
        admin_log('edited', 'gallery_album', $id, 'Gallery album edited: ' . $album['title']);
        admin_flash('success', 'Gallery album updated.');
        header('Location: ' . admin_url('gallery/'));
        exit;
    }
}
$adminTitle = 'Edit Gallery Album';
$activeNav = 'gallery';
require __DIR__ . '/../includes/admin-header.php';
$titleMap = e(json_encode(admin_gallery_album_titles()));
[$categorySelect, $categoryCustom] = admin_select_other_state($album['category'], admin_content_programmes());
?>
<section class="admin-panel"><?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?><form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><div class="admin-form-grid"><div class="admin-field"><label>Programme / Category</label><select class="admin-select" name="category_select" data-other-select="category_custom" data-programme-title-source="gallery-title" data-programme-title-map="<?php echo $titleMap; ?>" required><option value="">Select programme / category</option><?php foreach (admin_content_programmes() as $option) : ?><option value="<?php echo e($option); ?>" <?php echo $categorySelect === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option><?php endforeach; ?><option value="Others" <?php echo $categorySelect === 'Others' ? 'selected' : ''; ?>>Others</option></select><div class="admin-field" data-other-field="category_custom" <?php echo $categorySelect === 'Others' ? '' : 'hidden'; ?>><label>Custom Programme / Category</label><input class="admin-input" name="category_custom" value="<?php echo e($categoryCustom); ?>" <?php echo $categorySelect === 'Others' ? 'required' : ''; ?>></div></div><?php admin_render_select_other('title', 'Album Title', $album['title'], $titleOptions, 'Custom Album Title', true); ?></div><div class="admin-form-grid"><?php admin_render_select_other('location', 'Location', $album['location'], admin_content_locations(), 'Custom Location', true); ?><div class="admin-field"><label>Activity Date</label><input class="admin-input" type="date" name="activity_date" value="<?php echo e($album['activity_date']); ?>"></div></div><div class="admin-field"><label>Description</label><textarea class="admin-textarea" name="description"><?php echo e($album['description']); ?></textarea></div><?php admin_gallery_render_photo_uploader((int) $galleryPhotoCount); ?><div class="admin-form-grid"><div class="admin-field"><label>Status</label><select class="admin-select" name="status"><option value="draft" <?php echo $album['status']==='draft'?'selected':''; ?>>Draft</option><option value="pending_review" <?php echo $album['status']==='pending_review'?'selected':''; ?>>Pending Review</option><option value="published" <?php echo $album['status']==='published'?'selected':''; ?>>Published</option><option value="archived" <?php echo $album['status']==='archived'?'selected':''; ?>>Archived</option></select></div></div><div class="admin-actions"><a class="admin-button admin-button--light" href="<?php echo admin_url('gallery/'); ?>">Back to List</a><?php foreach (admin_content_allowed_actions($album) as $workflowAction) : ?><button class="admin-button<?php echo $workflowAction === 'publish' ? '' : ' admin-button--light'; ?>" name="submit_action" value="<?php echo e($workflowAction); ?>" type="submit"><?php echo e(admin_content_action_label($workflowAction)); ?></button><?php endforeach; ?></div></form></section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var source = document.querySelector('[data-programme-title-source="gallery-title"]');
    var title = document.querySelector('select[name="title_select"]');
    if (!source || !title) {
        return;
    }
    var map = {};
    try {
        map = JSON.parse(source.getAttribute('data-programme-title-map') || '{}');
    } catch (error) {}
    source.addEventListener('change', function () {
        if (map[source.value] && (!title.value || title.value !== 'Others')) {
            title.value = map[source.value];
            title.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
});
</script>
<?php admin_gallery_photo_uploader_script(); ?>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
