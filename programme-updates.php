<?php
$pageTitle = 'Programme Updates';
$pageDescription = 'Published programme progress and achievement updates from SSVP South Sudan.';
$assetVersion = 'programme-updates-public-v1';
require_once __DIR__ . '/includes/header.php';

$programmeUpdates = ssvdp_public_programme_updates(0);
?>

<div class="news-page programme-updates-page">
    <section class="newsroom-hero section-reveal" aria-labelledby="programme-updates-page-title">
        <div class="container newsroom-hero__inner">
            <div class="newsroom-hero__copy">
                <p class="newsroom-kicker">Programme Updates</p>
                <h1 id="programme-updates-page-title">Progress From Our Programmes</h1>
                <span class="newsroom-hero__rule" aria-hidden="true"></span>
                <p>See published progress updates, achievements and field activity highlights from SSVP South Sudan programmes.</p>
            </div>
            <div class="newsroom-hero__media" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/news/news-hero-health.jpg'); ?>" alt="" loading="eager" width="640" height="420">
            </div>
        </div>
    </section>

    <section class="newsroom-latest section-reveal" aria-labelledby="programme-updates-list-heading">
        <div class="container">
            <div class="newsroom-heading-row">
                <h2 id="programme-updates-list-heading">Published Programme Updates</h2>
                <a href="<?php echo site_url('news.php'); ?>">Back to News <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <?php if ($programmeUpdates) : ?>
                <div class="newsroom-card-grid programme-updates-card-grid">
                    <?php foreach ($programmeUpdates as $update) : ?>
                        <article class="newsroom-card programme-update-card">
                            <?php if ($update['featured_image'] !== '') : ?>
                                <a class="newsroom-card__image" href="<?php echo site_url($update['link']); ?>">
                                    <img src="<?php echo site_url($update['featured_image']); ?>" alt="<?php echo e($update['title']); ?>" loading="lazy" width="420" height="260">
                                </a>
                            <?php endif; ?>
                            <div class="newsroom-card__body">
                                <p class="story-meta"><?php echo e($update['programme']); ?><?php echo $update['date'] !== '' ? ' <span aria-hidden="true">&bull;</span> ' . e($update['date']) : ''; ?></p>
                                <h3><a href="<?php echo site_url($update['link']); ?>"><?php echo e($update['title']); ?></a></h3>
                                <?php if ($update['location'] !== '') : ?><p class="event-card-location"><i class="bi bi-geo-alt" aria-hidden="true"></i> <?php echo e($update['location']); ?></p><?php endif; ?>
                                <?php if ($update['short_description'] !== '') : ?><p><?php echo e($update['short_description']); ?></p><?php endif; ?>
                                <a class="newsroom-read-more" href="<?php echo site_url($update['link']); ?>">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="newsroom-empty">No published programme updates are currently available.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
