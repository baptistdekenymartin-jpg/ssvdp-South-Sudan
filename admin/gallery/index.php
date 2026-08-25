<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
admin_require_csrf();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    if ($id > 0 && in_array($action, array('publish','draft'), true)) {
        $pdo->prepare('UPDATE gallery_albums SET status = ? WHERE id = ?')->execute([$action === 'publish' ? 'published' : 'draft', $id]);
        admin_log($action === 'publish' ? 'published' : 'unpublished', 'gallery_album', $id, 'Gallery album status updated.');
        admin_flash('success', 'Gallery album updated.');
    }
    header('Location: ' . admin_url('gallery/'));
    exit;
}
$albums = $pdo->query('SELECT a.*, COUNT(p.id) AS photo_count FROM gallery_albums a LEFT JOIN gallery_photos p ON p.album_id = a.id GROUP BY a.id ORDER BY a.updated_at DESC, a.id DESC')->fetchAll();
$adminTitle = 'Gallery';
$activeNav = 'gallery';
require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar"><h2 style="margin:0;color:#063b8f">Gallery Albums</h2><a class="admin-button" href="<?php echo admin_url('gallery/create.php'); ?>"><i class="bi bi-plus-lg" aria-hidden="true"></i> Create Album</a></div>
<section class="admin-table-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Cover</th><th>Album Name</th><th>Programme / Category</th><th>Photos</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($albums as $album) : ?><tr><td><?php if ($album['cover_image']) : ?><img class="admin-thumb" src="<?php echo site_url($album['cover_image']); ?>" alt=""><?php endif; ?></td><td><strong><?php echo e($album['title']); ?></strong><br><small><?php echo e($album['location']); ?></small></td><td><?php echo e($album['category']); ?></td><td><?php echo (int) $album['photo_count']; ?></td><td><?php echo e(ssvdp_format_date($album['activity_date'], '')); ?></td><td><span class="admin-status admin-status--<?php echo e($album['status']); ?>"><?php echo e(ucfirst($album['status'])); ?></span></td><td><div class="admin-row-actions"><a href="<?php echo admin_url('gallery/album.php?id=' . (int) $album['id']); ?>">Manage</a><form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $album['id']; ?>"><input type="hidden" name="action" value="<?php echo $album['status'] === 'published' ? 'draft' : 'publish'; ?>"><button type="submit"><?php echo $album['status'] === 'published' ? 'Unpublish' : 'Publish'; ?></button></form></div></td></tr><?php endforeach; ?>
<?php if (!$albums) : ?><tr><td colspan="7">No gallery albums yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
