<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$errors = array();
$album = array('title'=>'','category'=>'','activity_date'=>'','location'=>'','description'=>'','status'=>'draft');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    foreach ($album as $key => $value) { $album[$key] = trim((string) ($_POST[$key] ?? '')); }
    if ($album['title'] === '') { $errors[] = 'Album title is required.'; }
    if ($album['category'] === '') { $errors[] = 'Programme / category is required.'; }
    $cover = '';
    if (!empty($_FILES['cover_image']['name'])) { try { $cover = admin_store_upload($_FILES['cover_image'], 'gallery'); } catch (Throwable $e) { $errors[] = $e->getMessage(); } }
    if (!$errors) {
        $slug = admin_slug_unique($pdo, 'gallery_albums', $album['title']);
        $stmt = $pdo->prepare('INSERT INTO gallery_albums (title, slug, category, activity_date, location, description, cover_image, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$album['title'], $slug, $album['category'], $album['activity_date'] ?: null, $album['location'], $album['description'], $cover, $album['status'] === 'published' ? 'published' : 'draft', (int) $adminUser['id']]);
        $id = (int) $pdo->lastInsertId();
        admin_log('created', 'gallery_album', $id, 'Gallery album created: ' . $album['title']);
        admin_flash('success', 'Gallery album created.');
        header('Location: ' . admin_url('gallery/album.php?id=' . $id));
        exit;
    }
}
$adminTitle = 'Create Gallery Album';
$activeNav = 'gallery';
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?><form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><div class="admin-form-grid"><div class="admin-field"><label>Album Title</label><input class="admin-input" name="title" value="<?php echo e($album['title']); ?>" required></div><div class="admin-field"><label>Programme / Category</label><input class="admin-input" name="category" value="<?php echo e($album['category']); ?>" required></div></div><div class="admin-form-grid"><div class="admin-field"><label>Activity Date</label><input class="admin-input" type="date" name="activity_date" value="<?php echo e($album['activity_date']); ?>"></div><div class="admin-field"><label>Location</label><input class="admin-input" name="location" value="<?php echo e($album['location']); ?>"></div></div><div class="admin-field"><label>Description</label><textarea class="admin-textarea" name="description"><?php echo e($album['description']); ?></textarea></div><div class="admin-form-grid"><div class="admin-field"><label>Cover Image</label><input class="admin-input" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div><div class="admin-field"><label>Status</label><select class="admin-select" name="status"><option value="draft">Draft</option><option value="published">Publish Album</option></select></div></div><div class="admin-actions"><button class="admin-button admin-button--light" name="status" value="draft" type="submit">Save Draft</button><button class="admin-button" name="status" value="published" type="submit">Publish Album</button></div></form></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
