<?php
$pageTitle = 'Our Work';
$pageDescription = 'Explore SSVP South Sudan programmes, featured projects, areas of operation and impact.';
$assetVersion = 'work-cta-about-match-v1';
require_once __DIR__ . '/includes/header.php';

$workPage = $siteConfig['ourWorkPage'];
?>

<div class="work-page our-work-programmes-page">
    <section class="work-hero section-reveal" style="--work-hero-image: url('<?php echo site_url($workPage['hero']['image']); ?>');">
        <div class="container work-hero-inner">
            <div class="work-hero-copy">
                <p class="section-label"><?php echo e($workPage['hero']['label']); ?></p>
                <h1><?php echo e($workPage['hero']['heading']); ?></h1>
                <p><?php echo e($workPage['hero']['text']); ?></p>
                <div class="hero-actions">
                    <?php foreach ($workPage['hero']['buttons'] as $button) : ?>
                        <a class="btn <?php echo e($button['class']); ?>" href="<?php echo site_url($button['link']); ?>"><?php echo e($button['label']); ?> <i class="bi <?php echo e($button['icon']); ?>" aria-hidden="true"></i></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="work-intro section-reveal">
        <div class="work-simple-content">
            <p class="work-label">HOW WE SERVE</p>
            <h2><?php echo e($workPage['intro']['heading']); ?></h2>
            <p><?php echo e($workPage['intro']['text']); ?></p>
        </div>
    </section>

    <section class="work-section programme-areas-section section-reveal" id="programmes">
        <div class="work-section-heading">
            <p class="work-label">PROGRAMMES</p>
            <h2><?php echo e($workPage['programme_areas']['heading']); ?></h2>
            <p><?php echo e($workPage['programme_areas']['text']); ?></p>
        </div>
        <div class="programme-areas-grid">
            <?php foreach ($workPage['programme_areas']['items'] as $item) : ?>
                <article class="programme-area-card">
                    <i class="bi <?php echo e($item['icon']); ?>" aria-hidden="true"></i>
                    <h3><?php echo e($item['title']); ?></h3>
                    <p><?php echo e($item['description']); ?></p>
                    <a class="learn-more" href="<?php echo site_url($item['link']); ?>">Learn More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="work-section featured-projects-section section-reveal" id="projects">
        <div class="work-section-heading">
            <p class="work-label">PROJECTS</p>
            <h2><?php echo e($workPage['featured_projects']['heading']); ?></h2>
            <p class="work-projects-intro"><?php echo e($workPage['featured_projects']['text']); ?></p>
        </div>
        <div class="featured-projects-grid">
            <?php foreach ($workPage['featured_projects']['items'] as $project) : ?>
                <article class="featured-project-card <?php echo e($project['class'] ?? ''); ?>">
                    <div class="featured-project-card__media">
                        <img class="featured-project-card__image" src="<?php echo site_url($project['image']); ?>" alt="" loading="lazy">
                    </div>
                    <div class="featured-project-card__content">
                        <?php if ($project['location'] !== '') : ?>
                            <p class="featured-project-card__location"><i class="bi bi-geo-alt" aria-hidden="true"></i><?php echo e($project['location']); ?></p>
                        <?php endif; ?>
                        <h3><?php echo e($project['title']); ?></h3>
                        <p><?php echo e($project['description']); ?></p>
                        <a class="btn btn-secondary-dark featured-project-card__link" href="<?php echo site_url($project['link']); ?>">View Project</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="work-areas section-reveal" id="areas">
        <div class="work-areas-copy">
            <p class="work-label">AREAS OF OPERATION</p>
            <h2><?php echo e($workPage['areas_preview']['heading']); ?></h2>
            <p><?php echo e($workPage['areas_preview']['text']); ?></p>
        </div>
        <div class="work-location-chips" aria-label="SSVP areas of operation">
            <?php foreach ($workPage['areas_preview']['locations'] as $location) : ?>
                <span><i class="bi bi-geo-alt" aria-hidden="true"></i><?php echo e($location); ?></span>
            <?php endforeach; ?>
        </div>
        <a class="btn btn-primary" href="<?php echo site_url($workPage['areas_preview']['button_link']); ?>"><?php echo e($workPage['areas_preview']['button_label']); ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </section>

    <section class="about-cta cta-photo-cta complete-prefooter-cta section-reveal">
        <div class="prefooter-cta-blue">
            <div class="container about-final-cta-inner cta-photo-cta-inner">
                <div class="cta-photo-cta-copy">
                    <h2 aria-label="<?php echo e($workPage['cta']['heading']); ?>">Together, We Can Build<br><span>Stronger</span> Communities</h2>
                    <p><?php echo e($workPage['cta']['text']); ?></p>
                    <div class="hero-actions">
                        <?php foreach ($workPage['cta']['buttons'] as $button) : ?>
                            <a class="btn <?php echo e($button['class']); ?>" href="<?php echo site_url($button['link']); ?>"><?php echo e($button['label']); ?> <i class="bi <?php echo e($button['icon']); ?>" aria-hidden="true"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <figure class="cta-photo-cta-photo">
                    <img src="<?php echo site_url('assets/images/work/women training.jpg'); ?>" alt="SSVP South Sudan community training activity" loading="lazy" width="760" height="520">
                </figure>
            </div>
        </div>

        <div class="prefooter-cta-lower">
            <div class="container prefooter-values-panel" aria-label="SSVP values">
                <article class="prefooter-value-item">
                    <span class="prefooter-value-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                    <div>
                        <h3>Compassion</h3>
                        <p>We serve with love and respect for every person.</p>
                    </div>
                </article>
                <article class="prefooter-value-item">
                    <span class="prefooter-value-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    <div>
                        <h3>Community</h3>
                        <p>We work together to strengthen and uplift our communities.</p>
                    </div>
                </article>
                <article class="prefooter-value-item">
                    <span class="prefooter-value-icon"><i class="bi bi-handshake" aria-hidden="true"></i></span>
                    <div>
                        <h3>Partnership</h3>
                        <p>We collaborate with partners for lasting impact and change.</p>
                    </div>
                </article>
                <article class="prefooter-value-item">
                    <span class="prefooter-value-icon"><i class="bi bi-flower1" aria-hidden="true"></i></span>
                    <div>
                        <h3>Empowerment</h3>
                        <p>We promote dignity, opportunity and self-reliance for all.</p>
                    </div>
                </article>
            </div>
            <div class="prefooter-cream-transition" aria-hidden="true"></div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
