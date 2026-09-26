<?php
require_once __DIR__ . '/includes/content-database.php';

$event = ssvdp_public_event_detail(isset($_GET['slug']) ? (string) $_GET['slug'] : null, (int) ($_GET['id'] ?? 0));
if (!$event) {
    http_response_code(404);
    $pageTitle = 'Event Not Found';
    $pageDescription = 'The requested event could not be found.';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="newsroom-latest section-reveal">
        <div class="container">
            <div class="newsroom-heading-row">
                <h1>Event Not Found</h1>
                <a href="<?php echo site_url('events.php'); ?>">View All Events <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <p class="newsroom-empty">This event is not published or no longer exists.</p>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $event['title'];
$pageDescription = $event['short_description'] !== '' ? $event['short_description'] : 'Event details from SSVP South Sudan.';
$assetVersion = 'events-public-v1';
require_once __DIR__ . '/includes/header.php';

function ssvdp_public_event_time_label(?string $time): string
{
    if (!$time || $time === '00:00:00') {
        return '';
    }
    $timestamp = strtotime($time);
    return $timestamp ? date('g:i A', $timestamp) : '';
}
?>

<div class="news-page event-detail-page">
    <section class="newsroom-hero event-detail-hero section-reveal" aria-labelledby="event-detail-title">
        <div class="container newsroom-hero__inner">
            <div class="newsroom-hero__copy">
                <p class="newsroom-kicker"><?php echo e($event['type']); ?></p>
                <h1 id="event-detail-title"><?php echo e($event['title']); ?></h1>
                <span class="newsroom-hero__rule" aria-hidden="true"></span>
                <?php if ($event['short_description'] !== '') : ?><p><?php echo e($event['short_description']); ?></p><?php endif; ?>
            </div>
            <?php if ($event['featured_image'] !== '') : ?>
                <div class="newsroom-hero__media">
                    <img src="<?php echo site_url($event['featured_image']); ?>" alt="<?php echo e($event['title']); ?>" loading="eager" width="640" height="420">
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="event-detail-section section-reveal">
        <div class="container event-detail-layout">
            <aside class="event-detail-meta" aria-label="Event information">
                <dl>
                    <div><dt>Event Type</dt><dd><?php echo e($event['type']); ?></dd></div>
                    <div><dt>Start Date</dt><dd><?php echo e(ssvdp_format_date($event['start_date'], '')); ?></dd></div>
                    <?php if ($event['end_date'] !== '') : ?><div><dt>End Date</dt><dd><?php echo e(ssvdp_format_date($event['end_date'], '')); ?></dd></div><?php endif; ?>
                    <?php if (ssvdp_public_event_time_label($event['start_time']) !== '') : ?><div><dt>Start Time</dt><dd><?php echo e(ssvdp_public_event_time_label($event['start_time'])); ?></dd></div><?php endif; ?>
                    <?php if (ssvdp_public_event_time_label($event['end_time']) !== '') : ?><div><dt>End Time</dt><dd><?php echo e(ssvdp_public_event_time_label($event['end_time'])); ?></dd></div><?php endif; ?>
                    <?php if ($event['location'] !== '') : ?><div><dt>Location</dt><dd><?php echo e($event['location']); ?></dd></div><?php endif; ?>
                </dl>
                <a class="newsroom-read-more" href="<?php echo site_url('events.php'); ?>">View All Events <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </aside>

            <article class="event-detail-content">
                <?php if ($event['short_description'] !== '') : ?>
                    <h2>Summary</h2>
                    <p><?php echo e($event['short_description']); ?></p>
                <?php endif; ?>
                <h2>Full Description</h2>
                <?php if ($event['full_description'] !== '') : ?>
                    <p><?php echo nl2br(e($event['full_description'])); ?></p>
                <?php else : ?>
                    <p>Full event details will be updated soon.</p>
                <?php endif; ?>
            </article>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
