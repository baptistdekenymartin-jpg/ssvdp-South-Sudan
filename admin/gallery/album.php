<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM gallery_albums WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$album = $stmt->fetch();
if (!$album) { admin_flash('error', 'Gallery album not found.'); header('Location: ' . admin_url('gallery/')); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'upload' && !empty($_FILES['photos']['name'][0])) {
        $uploaded = 0;
        foreach ($_FILES['photos']['name'] as $i => $name) {
            if ($name === '') { continue; }
            $file = array('name'=>$name,'type'=>$_FILES['photos']['type'][$i],'tmp_name'=>$_FILES['photos']['tmp_name'][$i],'error'=>$_FILES['photos']['error'][$i],'size'=>$_FILES['photos']['size'][$i]);
            try {
                $path = admin_store_upload($file, 'gallery');
                $caption = trim((string) ($_POST['captions'][$i] ?? ''));
                $pdo->prepare('INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) VALUES (?, ?, ?, ?)')->execute([$id, $path, $caption, $i]);
                if (!$album['cover_image']) { $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute([$path, $id]); $album['cover_image'] = $path; }
                $uploaded++;
            } catch (Throwable $e) { admin_flash('error', $e->getMessage()); }
        }
        if ($uploaded > 0) { admin_log('uploaded', 'gallery_album', $id, $uploaded . ' gallery photo(s) uploaded.'); admin_flash('success', $uploaded . ' photo(s) uploaded.'); }
    } elseif ($action === 'caption') {
        foreach ($_POST['captions_existing'] ?? array() as $photoId => $caption) {
            $pdo->prepare('UPDATE gallery_photos SET caption = ? WHERE id = ? AND album_id = ?')->execute([trim((string) $caption), (int) $photoId, $id]);
        }
        admin_log('edited', 'gallery_album', $id, 'Gallery captions updated.');
        admin_flash('success', 'Captions updated.');
    } elseif ($action === 'cover') {
        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $stmt = $pdo->prepare('SELECT image_path FROM gallery_photos WHERE id = ? AND album_id = ?');
        $stmt->execute([$photoId, $id]);
        $path = $stmt->fetchColumn();
        if ($path) { $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute([$path, $id]); admin_flash('success', 'Cover image updated.'); }
    } elseif ($action === 'remove') {
        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $pdo->prepare('DELETE FROM gallery_photos WHERE id = ? AND album_id = ?')->execute([$photoId, $id]);
        admin_log('removed', 'gallery_photo', $photoId, 'Gallery photo removed.');
        admin_flash('success', 'Photo removed.');
    }
    header('Location: ' . admin_url('gallery/album.php?id=' . $id));
    exit;
}
$photosStmt = $pdo->prepare('SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order, id');
$photosStmt->execute([$id]);
$photos = $photosStmt->fetchAll();
$adminTitle = 'Album: ' . $album['title'];
$activeNav = 'gallery';
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel" style="margin-bottom:22px"><div class="admin-toolbar"><div><h2><?php echo e($album['title']); ?></h2><p class="admin-muted"><?php echo e($album['category']); ?><?php echo $album['location'] ? ' | ' . e($album['location']) : ''; ?></p></div><a class="admin-button admin-button--light" href="<?php echo admin_url('gallery/'); ?>">Back to Gallery</a></div><form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><input type="hidden" name="action" value="upload"><div class="admin-field"><label>Upload Photos</label><input class="admin-input" type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small>Select multiple JPG, PNG or WEBP images.</small></div><button class="admin-button" type="submit">Upload Photos</button></form></section>
<section class="admin-panel"><h2>Photos</h2><?php if ($photos) : ?><form method="post" class="admin-form"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><input type="hidden" name="action" value="caption"><div class="admin-photo-grid"><?php foreach ($photos as $photo) : ?><article class="admin-photo-card"><img src="<?php echo site_url($photo['image_path']); ?>" alt=""><div><input class="admin-input" name="captions_existing[<?php echo (int) $photo['id']; ?>]" value="<?php echo e($photo['caption']); ?>" placeholder="Caption"><div class="admin-row-actions" style="margin-top:8px"><button formaction="<?php echo admin_url('gallery/album.php'); ?>" formmethod="post" name="action" value="cover" onclick="this.form.photo_id.value='<?php echo (int) $photo['id']; ?>'">Set Cover</button><button name="action" value="remove" onclick="this.form.photo_id.value='<?php echo (int) $photo['id']; ?>'; return confirm('Remove this photo?');">Remove</button></div></div></article><?php endforeach; ?></div><input type="hidden" name="photo_id" value=""><button class="admin-button" type="submit">Save Captions</button></form><?php else : ?><p class="admin-muted">No photos uploaded yet.</p><?php endif; ?></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
