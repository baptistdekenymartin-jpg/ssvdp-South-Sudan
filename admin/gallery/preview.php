<?php
require_once __DIR__ . '/../includes/auth.php';
admin_require_auth();
$pdo = admin_require_db();
$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM gallery_albums WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$album = $stmt->fetch();
if (!$album) { exit('Gallery album not found.'); }
$photosStmt = $pdo->prepare('SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order, id');
$photosStmt->execute([$id]);
$photos = $photosStmt->fetchAll();
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Preview: <?php echo e($album['title']); ?></title><link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>"></head><body class="admin-body"><main class="admin-content"><div class="admin-alert">Preview only. Visible to signed-in administrators and does not publish changes.</div><section class="admin-panel"><h1><?php echo e($album['title']); ?></h1><p><strong>Category:</strong> <?php echo e($album['category']); ?></p><p><strong>Date:</strong> <?php echo e(ssvdp_format_date($album['activity_date'], '')); ?></p><p><strong>Location:</strong> <?php echo e($album['location']); ?></p><p><strong>Status:</strong> <?php echo e(ucfirst($album['status'])); ?></p><p><?php echo nl2br(e($album['description'])); ?></p></section><section class="admin-panel" style="margin-top:18px"><h2>Photos</h2><?php if ($photos) : ?><div class="admin-photo-grid"><?php foreach ($photos as $photo) : ?><article class="admin-photo-card"><img src="<?php echo site_url($photo['image_path']); ?>" alt=""><div><?php echo e($photo['caption']); ?></div></article><?php endforeach; ?></div><?php else : ?><p class="admin-muted">No photos uploaded yet.</p><?php endif; ?></section></main></body></html>