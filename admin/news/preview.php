<?php
require_once __DIR__ . '/../includes/news-form.php';
admin_require_auth();
$pdo = admin_require_db();
$images = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $story = array(
        'title' => trim((string) ($_POST['title'] ?? 'Untitled News')),
        'category' => trim((string) ($_POST['category'] ?? 'Programme Update')),
        'location' => trim((string) ($_POST['location'] ?? '')),
        'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
        'content' => admin_news_sanitize_html((string) ($_POST['content'] ?? '')),
        'featured_image' => '',
        'published_at' => trim((string) ($_POST['published_at'] ?? date('Y-m-d')))
    );
} else {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $story = $stmt->fetch();
    if (!$story) { exit('News story not found.'); }
    $imageStmt = $pdo->prepare('SELECT * FROM news_images WHERE news_id = ? ORDER BY sort_order, id');
    $imageStmt->execute([$id]);
    $images = $imageStmt->fetchAll();
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Preview: <?php echo e($story['title']); ?></title><link rel="stylesheet" href="<?php echo site_url('assets/css/style.css'); ?>"><link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>"></head><body>
<div class="admin-alert" style="margin:18px">Preview only. This is visible to signed-in administrators.</div>
<main class="news-article" style="margin-top:20px">
    <div class="container news-article-inner">
        <div class="news-article-meta"><span><?php echo e($story['category']); ?></span><span><?php echo e(ssvdp_format_date($story['published_at'] ?? null)); ?></span><?php if (!empty($story['location'])) : ?><span><?php echo e($story['location']); ?></span><?php endif; ?></div>
        <h1 style="color:#063b8f"><?php echo e($story['title']); ?></h1>
        <?php if (!empty($story['featured_image'])) : ?><figure class="news-article-featured-image"><img src="<?php echo site_url($story['featured_image']); ?>" alt="<?php echo e($story['title']); ?>"></figure><?php endif; ?>
        <div class="news-article-body"><p><strong><?php echo e($story['excerpt']); ?></strong></p><?php echo $story['content']; ?></div>
        <?php foreach ($images as $image) : ?><figure class="news-article-inline-image"><img src="<?php echo site_url($image['image_path']); ?>" alt="<?php echo e($image['caption'] ?: $story['title']); ?>"></figure><?php endforeach; ?>
    </div>
</main>
</body></html>