<?php
$pageTitle = 'Projects';
$pageDescription = 'Featured SSVDP South Sudan projects.';
$assetVersion = 'projects-intro-v1';
require_once __DIR__ . '/includes/header.php';

$workPage = $siteConfig['ourWorkPage'];
?>

<div class="work-page route-page">
    <section class="work-section section-reveal">
        <div class="work-section-heading">
            <p class="work-label">PROJECTS</p>
            <h1><?php echo e($workPage['featured_projects']['heading']); ?></h1>
            <p class="work-projects-intro"><?php echo e($workPage['featured_projects']['text']); ?></p>
        </div>
        <div class="work-project-grid">
            <?php foreach ($workPage['featured_projects']['items'] as $project) : ?>
                <article class="work-project-card <?php echo e($project['class'] ?? ''); ?>">
                    <div class="work-card-media">
                        <img src="<?php echo site_url($project['image']); ?>" alt="" loading="lazy">
                    </div>
                    <div class="work-project-body">
                        <?php if ($project['location'] !== '') : ?>
                            <p class="work-location"><i class="bi bi-geo-alt" aria-hidden="true"></i><?php echo e($project['location']); ?></p>
                        <?php endif; ?>
                        <h3><?php echo e($project['title']); ?></h3>
                        <p><?php echo e($project['description']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>



