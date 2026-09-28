<?php
$pageTitle = 'News & Updates';
$pageDescription = 'Latest news and updates from SSVP South Sudan.';
$assetVersion = 'news-page-redesign-v1';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/public-submissions.php';
$newsletterResult = ssvdp_handle_newsletter_submission();

$primaryStory = array(
    'category' => "Health, Nutrition and Women's Empowerment",
    'filter' => 'training',
    'title' => "Women's Training in Nutrition, Hygiene and Family Planning",
    'description' => 'Thirty women participated in training on nutrition, hygiene and family planning in Nyarjwa Village as part of an empowerment programme for mothers and caregivers.',
    'date' => '13 July 2026',
    'image' => 'assets/images/work/women training.jpg',
    'image_alt' => 'SSVP women participating in a nutrition, hygiene and family planning training activity',
    'link' => 'news.php'
);

$newsStories = array(
    $primaryStory
);
$publicStories = ssvdp_public_news($newsStories);
$featuredMatches = array_values(array_filter($publicStories, static function ($story) { return !empty($story['is_featured']); }));
$primaryStory = $featuredMatches[0] ?? $publicStories[0];
$remainingStories = array_values(array_filter($publicStories, static function ($story) use ($primaryStory) { return $story['title'] !== $primaryStory['title']; }));
$sideStories = array_slice($remainingStories, 0, 2);
$latestGridStories = array_slice($remainingStories, 2, 3);
$communityStories = array();
$publicEvents = ssvdp_public_events(3);
$publicProgrammeUpdates = ssvdp_public_programme_updates(3);
$filters = array(
    array('key' => 'all', 'label' => 'All', 'icon' => 'bi-grid-3x3-gap'),
    array('key' => 'programmes', 'label' => 'Programmes', 'icon' => 'bi-briefcase'),
    array('key' => 'community-activities', 'label' => 'Community Activities', 'icon' => 'bi-people'),
    array('key' => 'training', 'label' => 'Training', 'icon' => 'bi-mortarboard'),
    array('key' => 'emergency-support', 'label' => 'Emergency Support', 'icon' => 'bi-life-preserver'),
    array('key' => 'partnerships', 'label' => 'Partnerships', 'icon' => 'bi-handshake'),
    array('key' => 'events', 'label' => 'Events', 'icon' => 'bi-calendar-event')
);
?>

<div class="news-page news-index-page">
    <section class="newsroom-hero section-reveal" aria-labelledby="news-page-title">
        <div class="container newsroom-hero__inner">
            <div class="newsroom-hero__copy">
                <p class="newsroom-kicker">Latest from SSVP</p>
                <h1 id="news-page-title">Stories of Service,<br>Hope &amp; Community</h1>
                <span class="newsroom-hero__rule" aria-hidden="true"></span>
                <p>Stay updated with our latest activities, programmes and stories from across South Sudan.</p>
            </div>
            <div class="newsroom-hero__media" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/news/news-hero-health.jpg'); ?>" alt="" loading="eager" width="640" height="420">
            </div>
        </div>
    </section>

    <section class="newsroom-featured section-reveal" aria-labelledby="featured-news-heading">
        <div class="container">
            <div class="newsroom-section-title">
                <p class="newsroom-kicker">Featured News</p>
                <h2 id="featured-news-heading">Latest Stories From Our Work</h2>
            </div>
            <div class="newsroom-featured-grid">
                <article class="featured-story-card">
                    <img src="<?php echo site_url($primaryStory['image']); ?>" alt="<?php echo e($primaryStory['image_alt']); ?>" loading="lazy" width="820" height="540">
                    <div class="featured-story-card__overlay">
                        <span class="story-badge">Featured Story</span>
                        <p class="story-meta"><?php echo e($primaryStory['date']); ?> <span aria-hidden="true">&bull;</span> <?php echo e($primaryStory['category']); ?></p>
                        <h3><?php echo e($primaryStory['title']); ?></h3>
                        <p><?php echo e($primaryStory['description']); ?></p>
                        <a class="featured-story-link" href="<?php echo site_url($primaryStory['link']); ?>">Read Full Story <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </article>

                <div class="newsroom-side-stack">
                    <?php foreach ($sideStories as $story) : ?>
                        <article class="side-story-card">
                            <div class="side-story-card__image">
                                <img src="<?php echo site_url($story['image']); ?>" alt="<?php echo e($story['image_alt']); ?>" loading="lazy" width="420" height="220">
                            </div>
                            <div class="side-story-card__content">
                                <span class="story-badge"><?php echo e($story['category']); ?></span>
                                <p class="story-meta"><?php echo e($story['date']); ?> <span aria-hidden="true">&bull;</span> <?php echo e($story['category']); ?></p>
                                <a href="<?php echo site_url($story['link']); ?>">
                                    <span><?php echo e($story['title']); ?></span>
                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if ($latestGridStories) : ?>
    <section class="newsroom-filters section-reveal" aria-label="News categories">
        <div class="container">
            <div class="newsroom-filter-bar" data-news-filters>
                <?php foreach ($filters as $index => $filter) : ?>
                    <button class="newsroom-filter<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-news-filter="<?php echo e($filter['key']); ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                        <i class="bi <?php echo e($filter['icon']); ?>" aria-hidden="true"></i>
                        <span><?php echo e($filter['label']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="newsroom-latest section-reveal" aria-labelledby="latest-news-heading">
        <div class="container">
            <div class="newsroom-heading-row">
                <h2 id="latest-news-heading">Latest News &amp; Updates</h2>
                <a href="<?php echo site_url('news.php'); ?>">View All News <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="newsroom-card-grid" data-news-grid>
                <?php foreach ($latestGridStories as $story) : ?>
                    <article class="newsroom-card" data-news-card data-news-category="<?php echo e($story['filter']); ?>">
                        <a class="newsroom-card__image" href="<?php echo site_url($story['link']); ?>">
                            <img src="<?php echo site_url($story['image']); ?>" alt="<?php echo e($story['image_alt']); ?>" loading="lazy" width="420" height="260">
                        </a>
                        <div class="newsroom-card__body">
                            <p class="story-meta"><?php echo e($story['date']); ?> <span aria-hidden="true">&bull;</span> <?php echo e($story['category']); ?></p>
                            <h3><a href="<?php echo site_url($story['link']); ?>"><?php echo e($story['title']); ?></a></h3>
                            <p><?php echo e($story['description']); ?></p>
                            <a class="newsroom-read-more" href="<?php echo site_url($story['link']); ?>">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="newsroom-empty" data-news-empty hidden>No updates are currently listed for this category.</p>
        </div>
    </section>

    <?php endif; ?>

    <?php if ($communityStories || $publicEvents || $publicProgrammeUpdates) : ?>
    <section class="newsroom-digest section-reveal" aria-label="Community stories, announcements and programme updates">
        <div class="container newsroom-digest-grid">
            <?php if ($communityStories) : ?>
            <article class="digest-panel">
                <h2>From Our Communities</h2>
                <span class="digest-rule" aria-hidden="true"></span>
                <div class="community-list">
                    <?php foreach ($communityStories as $story) : ?>
                        <a class="community-story" href="<?php echo site_url($story['link']); ?>">
                            <img src="<?php echo site_url($story['image']); ?>" alt="<?php echo e($story['image_alt'] ?? ''); ?>" loading="lazy" width="86" height="66">
                            <span>
                                <strong><?php echo e($story['title']); ?></strong>
                                <small><?php echo e($story['date']); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a class="digest-link" href="<?php echo site_url('gallery.php'); ?>">View All Community Stories <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </article>
            <?php endif; ?>

            <?php if ($publicEvents) : ?>
            <article class="digest-panel">
                <h2>Announcements &amp; Events</h2>
                <span class="digest-rule" aria-hidden="true"></span>
                <div class="event-list">
                    <?php foreach ($publicEvents as $event) : ?>
                        <div class="event-item">
                            <span class="event-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
                            <div>
                                <strong><a href="<?php echo site_url($event['link']); ?>"><?php echo e($event['title']); ?></a></strong>
                                <small><?php echo e(ssvdp_format_date($event['start_date'], '')); ?><?php echo $event['location'] ? ' | ' . e($event['location']) : ''; ?></small>
                                <?php if ($event['short_description'] !== '') : ?><p><?php echo e($event['short_description']); ?></p><?php endif; ?>
                                <a class="event-read-more" href="<?php echo site_url($event['link']); ?>">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a class="digest-link" href="<?php echo site_url('events.php'); ?>">View All Events <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </article>
            <?php endif; ?>

            <?php if ($publicProgrammeUpdates) : ?>
            <article class="digest-panel digest-panel--impact">
                <h2><span>Programme Updates</span></h2>
                <span class="digest-rule" aria-hidden="true"></span>
                <div class="impact-update-list programme-update-list">
                    <?php foreach ($publicProgrammeUpdates as $update) : ?>
                        <article class="impact-update programme-update-item">
                            <?php if ($update['featured_image'] !== '') : ?>
                                <a class="programme-update-thumb" href="<?php echo site_url($update['link']); ?>">
                                    <img src="<?php echo site_url($update['featured_image']); ?>" alt="<?php echo e($update['title']); ?>" loading="lazy" width="96" height="72">
                                </a>
                            <?php else : ?>
                                <span><i class="bi bi-check2" aria-hidden="true"></i></span>
                            <?php endif; ?>
                            <div class="programme-update-summary">
                                <h3><a href="<?php echo site_url($update['link']); ?>"><?php echo e($update['title']); ?></a></h3>
                                <small><?php echo e($update['programme']); ?><?php echo $update['date'] !== '' ? ' | ' . e($update['date']) : ''; ?><?php echo $update['location'] !== '' ? ' | ' . e($update['location']) : ''; ?></small>
                                <?php if ($update['short_description'] !== '') : ?><p><?php echo e($update['short_description']); ?></p><?php endif; ?>
                                <a class="event-read-more" href="<?php echo site_url($update['link']); ?>">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <a class="digest-link" href="<?php echo site_url('programme-updates.php'); ?>">View All Programme Updates <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </article>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
    <section class="newsroom-newsletter section-reveal" aria-labelledby="newsletter-heading">
        <div class="container">
            <div class="newsletter-band">
                <span class="newsletter-icon"><i class="bi bi-envelope-paper" aria-hidden="true"></i></span>
                <div class="newsletter-copy">
                    <h2 id="newsletter-heading">Stay Connected</h2>
                    <p>Subscribe to our newsletter and receive the latest updates and stories from SSVP South Sudan.</p>
                </div>
                <form class="newsletter-form" action="<?php echo site_url('news.php'); ?>#newsletter-heading" method="post" aria-label="Newsletter signup form">
                    <input type="hidden" name="form_type" value="newsletter">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <div class="form-honeypot" aria-hidden="true"><label for="newsletter-website">Website</label><input type="text" id="newsletter-website" name="website" tabindex="-1" autocomplete="off"></div>
                    <label class="sr-only" for="newsletter-email">Enter your email address</label>
                    <input id="newsletter-email" name="email" type="email" placeholder="Enter your email address" autocomplete="email" value="<?php echo ssvdp_form_old('email', $newsletterResult['values']); ?>">
                    <button type="submit">Subscribe</button>
                </form>
                <?php if ($newsletterResult['success']) : ?><div class="form-alert form-alert--success"><?php echo e($newsletterResult['success']); ?></div><?php endif; ?>
                <?php foreach ($newsletterResult['errors'] as $error) : ?><div class="form-alert form-alert--error"><?php echo e($error); ?></div><?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterBar = document.querySelector('[data-news-filters]');
    const cards = Array.from(document.querySelectorAll('[data-news-card]'));
    const emptyMessage = document.querySelector('[data-news-empty]');

    if (!filterBar || cards.length === 0) {
        return;
    }

    filterBar.addEventListener('click', function (event) {
        const button = event.target.closest('[data-news-filter]');

        if (!button) {
            return;
        }

        const filter = button.dataset.newsFilter;
        let visibleCount = 0;

        filterBar.querySelectorAll('[data-news-filter]').forEach(function (item) {
            const isActive = item === button;
            item.classList.toggle('is-active', isActive);
            item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        cards.forEach(function (card) {
            const shouldShow = filter === 'all' || card.dataset.newsCategory === filter;
            card.hidden = !shouldShow;
            if (shouldShow) {
                visibleCount += 1;
            }
        });

        if (emptyMessage) {
            emptyMessage.hidden = visibleCount > 0;
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
