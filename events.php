<?php
$pageTitle = 'Events & Announcements';
$pageDescription = 'Upcoming and published SSVP South Sudan events and announcements.';
$assetVersion = 'events-public-v1';
require_once __DIR__ . '/includes/header.php';

$events = ssvdp_public_events(0);
?>

<div class="news-page events-page">
    <section class="newsroom-hero section-reveal" aria-labelledby="events-page-title">
        <div class="container newsroom-hero__inner">
            <div class="newsroom-hero__copy">
                <p class="newsroom-kicker">Events &amp; Announcements</p>
                <h1 id="events-page-title">Upcoming Events</h1>
                <span class="newsroom-hero__rule" aria-hidden="true"></span>
                <p>See published workshops, meetings, announcements and community activities from SSVP South Sudan.</p>
            </div>
            <div class="newsroom-hero__media" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/news/news-hero-health.jpg'); ?>" alt="" loading="eager" width="640" height="420">
            </div>
        </div>
    </section>

    <section class="newsroom-latest section-reveal" aria-labelledby="events-list-heading">
        <div class="container">
            <div class="newsroom-heading-row">
                <h2 id="events-list-heading">Published Events</h2>
                <a href="<?php echo site_url('news.php'); ?>">Back to News <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <?php if ($events) : ?>
                <div class="newsroom-card-grid events-card-grid">
                    <?php foreach ($events as $event) : ?>
                        <article class="newsroom-card event-card">
                            <?php if ($event['featured_image'] !== '') : ?>
                                <a class="newsroom-card__image" href="<?php echo site_url($event['link']); ?>">
                                    <img src="<?php echo site_url($event['featured_image']); ?>" alt="<?php echo e($event['title']); ?>" loading="lazy" width="420" height="260">
                                </a>
                            <?php endif; ?>
                            <div class="newsroom-card__body">
                                <p class="story-meta"><?php echo e($event['type']); ?> <span aria-hidden="true">&bull;</span> <?php echo e(ssvdp_format_date($event['start_date'], '')); ?></p>
                                <h3><a href="<?php echo site_url($event['link']); ?>"><?php echo e($event['title']); ?></a></h3>
                                <?php if ($event['location'] !== '') : ?><p class="event-card-location"><i class="bi bi-geo-alt" aria-hidden="true"></i> <?php echo e($event['location']); ?></p><?php endif; ?>
                                <?php if ($event['short_description'] !== '') : ?><p><?php echo e($event['short_description']); ?></p><?php endif; ?>
                                <a class="newsroom-read-more" href="<?php echo site_url($event['link']); ?>">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="newsroom-empty">No published events are currently available.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
