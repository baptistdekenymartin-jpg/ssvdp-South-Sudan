<?php
$pageTitle = 'News Story';
$pageDescription = 'Latest news from SSVP South Sudan.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/news-content.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$story = null;
$images = array();
$pdo = ssvdp_db();
if ($slug !== '' && $pdo && ssvdp_table_exists($pdo, 'news')) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE slug = ? AND status = 'published' AND archived_at IS NULL LIMIT 1");
    $stmt->execute([$slug]);
    $story = $stmt->fetch();
    if ($story && ssvdp_table_exists($pdo, 'news_images')) {
        $imageStmt = $pdo->prepare('SELECT * FROM news_images WHERE news_id = ? ORDER BY sort_order, id');
        $imageStmt->execute([(int) $story['id']]);
        $images = $imageStmt->fetchAll();
    }
}
?>
<?php if (!$story) : ?>
<section class="news-article"><div class="container news-article-inner"><h1>News story not found</h1><p>The story may be unpublished or unavailable.</p><a class="btn btn-secondary-dark" href="<?php echo site_url('news.php'); ?>">Back to News &amp; Updates</a></div></section>
<?php else : ?>
<section class="news-hero news-article-hero section-reveal" aria-labelledby="article-title"><div class="container news-hero-inner"><div class="news-hero-copy"><p class="section-label">News &amp; Updates</p><h1 id="article-title"><?php echo e($story['title']); ?></h1><span class="news-hero-rule" aria-hidden="true"></span><p><?php echo e($story['excerpt']); ?></p></div></div></section>
<article class="news-article section-reveal"><div class="container news-article-inner"><div class="news-article-meta"><span><?php echo e($story['category']); ?></span><span><?php echo e(ssvdp_format_date($story['published_at'] ?: $story['created_at'])); ?></span><?php if ($story['location']) : ?><span><?php echo e($story['location']); ?></span><?php endif; ?></div><?php if ($story['featured_image']) : ?><figure class="news-article-featured-image"><img src="<?php echo site_url($story['featured_image']); ?>" alt="<?php echo e($story['title']); ?>" loading="lazy"></figure><?php endif; ?><div class="news-article-body"><?php echo ssvdp_news_sanitize_html((string) $story['content']); ?></div><?php foreach ($images as $image) : ?><figure class="news-article-inline-image"><img src="<?php echo site_url($image['image_path']); ?>" alt="<?php echo e($image['caption'] ?: $story['title']); ?>" loading="lazy"></figure><?php endforeach; ?><a class="btn btn-secondary-dark news-article-back" href="<?php echo site_url('news.php'); ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to News &amp; Updates</a></div></article>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
