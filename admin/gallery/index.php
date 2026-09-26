<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_permission('content.manage');
$pdo = admin_require_db();
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM gallery_albums LIKE 'status'");
    $column = $stmt ? $stmt->fetch() : null;
    $type = strtolower((string) ($column['Type'] ?? ''));
    if ($type !== '' && !str_contains($type, 'archived')) {
        $pdo->exec("ALTER TABLE gallery_albums MODIFY status ENUM('draft','pending_review','published','archived') NOT NULL DEFAULT 'draft'");
    }
} catch (Throwable $exception) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    if ($id > 0) {
        if (in_array($action, array('publish','unpublish','archive','return_draft','submit_review'), true)) {
            $stmt = $pdo->prepare('SELECT * FROM gallery_albums WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $targetAlbum = $stmt->fetch();
            if (!$targetAlbum || !admin_can_transition_content($action, $targetAlbum)) { admin_forbidden(); }
            $newStatus = admin_content_status_for_action($action, $targetAlbum);
            $pdo->prepare('UPDATE gallery_albums SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
            admin_log($action, 'gallery_album', $id, 'Gallery album workflow status changed.');
            admin_flash('success', 'Gallery album updated.');        } elseif ($action === 'delete') {
            if (!admin_is_administrator($adminUser)) { admin_forbidden(); }
            $stmt = $pdo->prepare('SELECT title FROM gallery_albums WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $title = (string) $stmt->fetchColumn();
            if ($title !== '') {
                $pdo->beginTransaction();
                try {
                    $pdo->prepare('DELETE FROM gallery_photos WHERE album_id = ?')->execute([$id]);
                    $pdo->prepare('DELETE FROM gallery_albums WHERE id = ?')->execute([$id]);
                    $pdo->commit();
                    admin_log('deleted', 'gallery_album', $id, 'Gallery album deleted permanently: ' . $title);
                    admin_flash('success', 'Gallery album deleted successfully.');
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) { $pdo->rollBack(); }
                    admin_flash('error', 'Could not delete the gallery album. Please try again.');
                }
            }
        }
    }
    header('Location: ' . admin_url('gallery/'));
    exit;
}
$status = trim((string) ($_GET['status'] ?? ''));
$where = array('1=1');
$params = array();
if (in_array($status, array('draft','pending_review','published','archived'), true)) { $where[] = 'a.status = ?'; $params[] = $status; }
$stmt = $pdo->prepare('SELECT a.*, COUNT(p.id) AS photo_count, COALESCE((SELECT gp.image_path FROM gallery_photos gp WHERE gp.album_id = a.id ORDER BY gp.sort_order, gp.id LIMIT 1), a.cover_image) AS thumbnail_image FROM gallery_albums a LEFT JOIN gallery_photos p ON p.album_id = a.id WHERE ' . implode(' AND ', $where) . ' GROUP BY a.id ORDER BY FIELD(a.status, \'published\', \'pending_review\', \'draft\', \'archived\'), a.updated_at DESC, a.id DESC');
$stmt->execute($params);
$albums = $stmt->fetchAll();
$adminTitle = 'Gallery';
$activeNav = 'gallery';
require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar"><form class="admin-filters" method="get"><select class="admin-select" style="width:170px" name="status"><option value="">All statuses</option><option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option><option value="pending_review" <?php echo $status === 'pending_review' ? 'selected' : ''; ?>>Pending Review</option><option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option><option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option></select><button class="admin-button admin-button--light" type="submit">Filter</button></form><a class="admin-button" href="<?php echo admin_url('gallery/create.php'); ?>"><i class="bi bi-plus-lg" aria-hidden="true"></i> Create Album</a></div>
<section class="admin-table-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Thumbnail</th><th>Album Name</th><th>Programme / Category</th><th>Photos</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($albums as $album) : $isPublished = $album['status'] === 'published'; ?><tr><td><?php if ($album['thumbnail_image']) : ?><img class="admin-thumb" src="<?php echo site_url($album['thumbnail_image']); ?>" alt=""><?php endif; ?></td><td><strong><?php echo e($album['title']); ?></strong><br><small><?php echo e($album['location']); ?></small></td><td><?php echo e($album['category']); ?></td><td><?php echo (int) $album['photo_count']; ?></td><td><?php echo e(ssvdp_format_date($album['activity_date'], '')); ?></td><td><span class="admin-status admin-status--<?php echo e($album['status']); ?>"><?php echo e(ucfirst($album['status'])); ?></span></td><td><div class="admin-row-actions"><a href="<?php echo admin_url('gallery/edit.php?id=' . (int) $album['id']); ?>">Edit</a><a href="<?php echo admin_url('gallery/preview.php?id=' . (int) $album['id']); ?>" target="_blank">Preview</a><a href="<?php echo admin_url('gallery/album.php?id=' . (int) $album['id']); ?>">Manage</a><form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $album['id']; ?>"><input type="hidden" name="action" value="<?php echo $isPublished ? 'unpublish' : 'publish'; ?>"><button type="submit"><?php echo $isPublished ? 'Unpublish' : 'Publish'; ?></button></form><form method="post" onsubmit="return confirm('Archive this gallery album? It will no longer appear publicly.');"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $album['id']; ?>"><input type="hidden" name="action" value="archive"><button type="submit">Archive</button></form><?php if (admin_is_administrator($adminUser)) : ?><button class="admin-row-delete-button" type="button" data-gallery-delete-trigger data-gallery-id="<?php echo (int) $album['id']; ?>" data-gallery-title="<?php echo e($album['title']); ?>">Delete</button><?php endif; ?></div></td></tr><?php endforeach; ?>
<?php if (!$albums) : ?><tr><td colspan="7">No gallery albums found.</td></tr><?php endif; ?>
</tbody></table></div></section>
<dialog class="admin-delete-dialog" data-gallery-delete-dialog><form method="post" data-gallery-delete-form><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value=""><h2>Delete Gallery Album</h2><p>Are you sure you want to delete this gallery album?</p><p><strong data-gallery-delete-title></strong></p><div class="admin-delete-dialog__actions"><button class="admin-button admin-button--light" type="button" data-gallery-delete-cancel>Cancel</button><button class="admin-button admin-button--danger" type="submit">Delete Permanently</button></div></form></dialog>
<script>document.addEventListener('DOMContentLoaded',function(){var d=document.querySelector('[data-gallery-delete-dialog]');if(!d){return;}var f=d.querySelector('[data-gallery-delete-form]'),i=f.querySelector('input[name="id"]'),t=d.querySelector('[data-gallery-delete-title]'),c=d.querySelector('[data-gallery-delete-cancel]');document.querySelectorAll('[data-gallery-delete-trigger]').forEach(function(b){b.addEventListener('click',function(){i.value=b.getAttribute('data-gallery-id')||'';t.textContent=b.getAttribute('data-gallery-title')||'';if(typeof d.showModal==='function'){d.showModal();}else if(window.confirm('Are you sure you want to delete this gallery album?\n\n'+t.textContent)){f.submit();}});});c.addEventListener('click',function(){d.close();});});</script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
