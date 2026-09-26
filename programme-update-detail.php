<?php
require_once __DIR__ . '/includes/content-database.php';

$programmeUpdate = ssvdp_public_programme_update_detail(isset($_GET['slug']) ? (string) $_GET['slug'] : null, (int) ($_GET['id'] ?? 0));
if (!$programmeUpdate) {
    http_response_code(404);
    $pageTitle = 'Programme Update Not Found';
    $pageDescription = 'The requested programme update could not be found.';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="newsroom-latest section-reveal">
        <div class="container">
            <div class="newsroom-heading-row">
                <h1>Programme Update Not Found</h1>
                <a href="<?php echo site_url('programme-updates.php'); ?>">View All Programme Updates <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <p class="newsroom-empty">This programme update is not published or no longer exists.</p>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $programmeUpdate['title'];
$pageDescription = $programmeUpdate['short_description'] !== '' ? $programmeUpdate['short_description'] : 'Programme update from SSVP South Sudan.';
$assetVersion = 'programme-updates-public-v1';
require_once __DIR__ . '/includes/header.php';
?>

<div class="news-page programme-update-detail-page">
    <section class="newsroom-hero event-detail-hero section-reveal" aria-labelledby="programme-update-detail-title">
        <div class="container newsroom-hero__inner">
            <div class="newsroom-hero__copy">
                <p class="newsroom-kicker"><?php echo e($programmeUpdate['programme']); ?></p>
                <h1 id="programme-update-detail-title"><?php echo e($programmeUpdate['title']); ?></h1>
                <span class="newsroom-hero__rule" aria-hidden="true"></span>
                <?php if ($programmeUpdate['short_description'] !== '') : ?><p><?php echo e($programmeUpdate['short_description']); ?></p><?php endif; ?>
            </div>
            <?php if ($programmeUpdate['featured_image'] !== '') : ?>
                <div class="newsroom-hero__media">
                    <img src="<?php echo site_url($programmeUpdate['featured_image']); ?>" alt="<?php echo e($programmeUpdate['title']); ?>" loading="eager" width="640" height="420">
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="event-detail-section section-reveal">
        <div class="container event-detail-layout">
            <aside class="event-detail-meta" aria-label="Programme update information">
                <dl>
                    <div><dt>Programme</dt><dd><?php echo e($programmeUpdate['programme']); ?></dd></div>
                    <?php if ($programmeUpdate['date'] !== '') : ?><div><dt>Date</dt><dd><?php echo e($programmeUpdate['date']); ?></dd></div><?php endif; ?>
                    <?php if ($programmeUpdate['location'] !== '') : ?><div><dt>Location</dt><dd><?php echo e($programmeUpdate['location']); ?></dd></div><?php endif; ?>
                </dl>
                <a class="newsroom-read-more" href="<?php echo site_url('programme-updates.php'); ?>">View All Programme Updates <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </aside>

            <article class="event-detail-content">
                <?php if ($programmeUpdate['short_description'] !== '') : ?>
                    <h2>Summary</h2>
                    <p><?php echo e($programmeUpdate['short_description']); ?></p>
                <?php endif; ?>
                <h2>Full Description</h2>
                <?php if ($programmeUpdate['full_description'] !== '') : ?>
                    <p><?php echo nl2br(e($programmeUpdate['full_description'])); ?></p>
                <?php else : ?>
                    <p>Full programme update details will be updated soon.</p>
                <?php endif; ?>
            </article>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
