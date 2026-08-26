<?php
$pageTitle = 'About SSVP South Sudan';
$pageDescription = 'Learn about SSVP South Sudan as a registered local nonprofit organization implementing humanitarian and development programmes with communities.';
$assetVersion = 'about-hero-no-panel-v1';
require_once __DIR__ . '/includes/header.php';

?>

<div class="about-page">
    <section class="about-hero">
        <div class="container about-hero-grid">
            <div class="about-hero-copy">
                <p class="section-label"><?php echo e($aboutPage['banner']['label']); ?></p>
                <h1><?php echo str_replace(', ', ',<br>', e($aboutPage['banner']['heading'])); ?></h1>
                <span class="about-hero-rule" aria-hidden="true"></span>
                <p class="about-hero-text"><?php echo e($aboutPage['banner']['text']); ?></p>
                <div class="about-hero-actions">
                    <a class="btn btn-primary" href="<?php echo site_url('about.php#about-ssvdp'); ?>"><i class="bi bi-people" aria-hidden="true"></i>Learn More About Us <i class="bi bi-chevron-right" aria-hidden="true"></i></a>
                    <a class="btn btn-outline-light" href="<?php echo site_url('programmes.php'); ?>"><i class="bi bi-heart" aria-hidden="true"></i>Our Work <i class="bi bi-chevron-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>
    <section class="about-overview section-reveal" id="about-ssvdp">
        <div class="about-overview-inner">
            <div class="about-overview-copy">
                <p class="about-overview-label"><?php echo e($aboutPage['introduction']['label']); ?></p>
                <h2>Serving Vulnerable Communities <span>with Compassion</span></h2>
                <span class="about-overview-heading-rule" aria-hidden="true"></span>
                <p><?php echo e($aboutPage['introduction']['paragraphs'][0]); ?></p>
            </div>

            <div class="about-overview-glance" aria-label="Organisation at a glance">
                <div class="about-overview-list">
                    <div class="about-overview-row about-overview-row--blue">
                        <span class="about-overview-icon"><i class="bi bi-patch-check" aria-hidden="true"></i></span>
                        <span><strong>Registered Status</strong><br><mark>Local Nonprofit Organization</mark></span>
                    </div>
                    <div class="about-overview-row about-overview-row--gold">
                        <span class="about-overview-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
                        <span><strong>Leadership</strong><br><mark>Executive Director</mark></span>
                    </div>
                    <div class="about-overview-row about-overview-row--blue">
                        <span class="about-overview-icon"><i class="bi bi-briefcase" aria-hidden="true"></i></span>
                        <span><strong>Work</strong><br><mark>Humanitarian &amp; Development Programmes</mark></span>
                    </div>
                    <div class="about-overview-row about-overview-row--gold">
                        <span class="about-overview-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                        <span><strong>Approach</strong><br><mark>Community Empowerment &amp; Participation</mark></span>
                    </div>
                    <div class="about-overview-row about-overview-row--blue">
                        <span class="about-overview-icon"><i class="bi bi-globe2" aria-hidden="true"></i></span>
                        <span><strong>Country</strong><br><mark>South Sudan</mark></span>
                    </div>
                </div>
            </div>

            <div class="about-overview-support">
                <div class="about-overview-support-icon" aria-hidden="true"><i class="bi bi-heart-pulse"></i></div>
                <div class="about-overview-support-text">
                    <p><?php echo e($aboutPage['introduction']['paragraphs'][1]); ?></p>
                    <p><?php echo e($aboutPage['introduction']['paragraphs'][2]); ?></p>
                </div>
            </div>
        </div>
        <div class="about-overview-wave" aria-hidden="true"></div>
    </section>
    <section class="who-we-are-section organization-section section-reveal" id="how-we-work">
        <div class="who-we-are-content organization-content">
            <div class="who-we-are-label">HOW WE WORK</div>
            <h2 class="who-we-are-title">Our Organization</h2>
            <?php foreach ($aboutPage['organization']['paragraphs'] as $paragraph) : ?>
                <p><?php echo e($paragraph); ?></p>
            <?php endforeach; ?>
            <div class="organization-feature-list organization-timeline" aria-label="SSVP organization process">
                <?php foreach ($aboutPage['organization']['features'] as $feature) : ?>
                    <article class="organization-feature-item organization-timeline-item">
                        <span class="organization-timeline-icon"><i class="bi <?php echo e($feature['icon']); ?>" aria-hidden="true"></i></span>
                        <div class="organization-timeline-copy">
                            <h3><?php echo e($feature['title']); ?></h3>
                            <p><?php echo e($feature['description']); ?></p>
                            <span class="organization-timeline-rule" aria-hidden="true"></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="mission-vision-section section-reveal" id="mission">
        <div class="mission-vision-intro">
            <p class="mission-vision-label">OUR PURPOSE &amp; DIRECTION</p>
            <h2>Our Mission &amp; Vision</h2>
            <p>Our mission drives the impact we create today, while our vision inspires the future we are building for a more compassionate and dignified South Sudan.</p>
        </div>

        <div class="mission-vision-connected" aria-label="Mission to vision pathway">
            <article class="mission-vision-panel mission-panel">
                <span class="mission-vision-floating-icon" aria-hidden="true"><i class="bi <?php echo e($aboutPage['mission']['icon']); ?>"></i></span>
                <div class="mission-vision-card-head">
                    <p class="mission-card-label">OUR MISSION</p>
                    <h3>Empowering individuals and communities.</h3>
                </div>
                <span class="mission-vision-panel-rule" aria-hidden="true"></span>
                <?php foreach ($aboutPage['mission']['paragraphs'] as $paragraph) : ?>
                    <p><?php echo e($paragraph); ?></p>
                <?php endforeach; ?>
            </article>

            <div class="mission-vision-center" aria-label="SSVP connects mission and vision">
                <span class="mission-chevron mission-chevron--blue" aria-hidden="true"></span>
                <div class="mission-logo-orbit">
                    <img src="<?php echo site_url('assets/images/logo/ssvdp-logo-cutout.png'); ?>" alt="SSVP South Sudan logo" loading="lazy" width="150" height="150">
                </div>
                <span class="mission-chevron mission-chevron--gold" aria-hidden="true"></span>
            </div>

            <article class="mission-vision-panel vision-panel" id="vision">
                <span class="mission-vision-floating-icon" aria-hidden="true"><i class="bi <?php echo e($aboutPage['vision']['icon']); ?>"></i></span>
                <div class="mission-vision-card-head">
                    <p class="mission-card-label">OUR VISION</p>
                    <h3>A self-reliant, peaceful and compassionate South Sudan.</h3>
                </div>
                <span class="mission-vision-panel-rule" aria-hidden="true"></span>
                <p><?php echo e($aboutPage['vision']['text']); ?></p>
            </article>
        </div>

        <div class="mission-values-strip" aria-label="Mission and vision values">
            <article class="mission-value-item mission-value-item--blue">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                <div class="mission-value-copy">
                    <h3>People First</h3>
                    <i aria-hidden="true"></i>
                    <p>We place vulnerable people and communities at the centre of our work.</p>
                </div>
            </article>
            <article class="mission-value-item mission-value-item--gold">
                <span><i class="bi bi-heart-fill" aria-hidden="true"></i></span>
                <div class="mission-value-copy">
                    <h3>Compassion in Action</h3>
                    <i aria-hidden="true"></i>
                    <p>We serve with love, dignity and practical care.</p>
                </div>
            </article>
            <article class="mission-value-item mission-value-item--blue">
                <span><i class="bi bi-handshake" aria-hidden="true"></i></span>
                <div class="mission-value-copy">
                    <h3>Stronger Together</h3>
                    <i aria-hidden="true"></i>
                    <p>We collaborate with communities and partners for shared impact.</p>
                </div>
            </article>
            <article class="mission-value-item mission-value-item--gold">
                <span><i class="bi bi-flower1" aria-hidden="true"></i></span>
                <div class="mission-value-copy">
                    <h3>Sustainable Impact</h3>
                    <i aria-hidden="true"></i>
                    <p>We build capacity for lasting dignity and self-reliance.</p>
                </div>
            </article>
        </div>
        <div class="mission-vision-wave" aria-hidden="true"></div>
    </section>
    <section class="values-section section-reveal" id="values">
        <div class="values-content">
            <div class="values-heading">
                <div class="values-kicker-icon" aria-hidden="true">
                    <span></span>
                    <i class="bi bi-heart"></i>
                    <span></span>
                </div>
                <h2>Our Values</h2>
                <p>These values guide everything we do. They reflect who we are, what we believe in, and how we serve our communities.</p>
            </div>
            <div class="values-grid">
                <?php
                $valuesByTitle = array();
                foreach ($aboutPage['values']['items'] as $value) {
                    $valuesByTitle[$value['title']] = $value;
                }
                $orderedValues = array('Compassion', 'Inclusivity', 'Integrity', 'Transparency', 'Peace', 'Accountability', 'Sustainability', 'Dignity');
                foreach ($orderedValues as $index => $title) :
                    if (!isset($valuesByTitle[$title])) {
                        continue;
                    }
                    $value = $valuesByTitle[$title];
                    $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                    $accentClass = in_array($title, array('Compassion', 'Integrity', 'Accountability', 'Dignity'), true) ? 'value-card--blue' : 'value-card--gold';
                ?>
                    <article class="value-card <?php echo e($accentClass); ?>">
                        <div class="value-card-icon">
                            <i class="bi <?php echo e($value['icon']); ?>" aria-hidden="true"></i>
                        </div>
                        <h3><?php echo e($value['title']); ?></h3>
                        <span class="value-card-rule" aria-hidden="true"></span>
                        <p><?php echo e($value['description']); ?></p>
                        <span class="value-card-number" aria-hidden="true"><?php echo e($number); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="values-mission-panel">
                <div class="values-mission-icon" aria-hidden="true">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h3>Together, these values strengthen our mission.</h3>
                    <p>They shape our work, inspire our team, and build trust with the communities we serve.</p>
                </div>
            </div>
        </div>
    <div class="values-wave" aria-hidden="true"></div>
    </section>
    <section class="history-section section-reveal" id="history">
        <div class="history-content">
            <div class="history-intro">
                <div class="history-label"><?php echo e($aboutPage['history']['label']); ?></div>
                <h2><?php echo e($aboutPage['history']['heading']); ?></h2>
                <span class="history-title-rule" aria-hidden="true"></span>
                <div class="history-logo-orbit" aria-label="SSVP South Sudan logo">
                    <img src="<?php echo site_url($siteConfig['logo']); ?>" alt="Society of St. Vincent de Paul South Sudan logo" loading="lazy" width="210" height="210">
                </div>
            </div>

            <div class="history-timeline" aria-label="SSVP Vincentian roots timeline">
                <article class="history-timeline-item">
                    <span class="history-timeline-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                    <div class="history-timeline-card">
                        <h3>1833 - The Beginning</h3>
                        <p>The Society of Saint Vincent de Paul traces its wider Vincentian tradition to 1833, when Frederic Ozanam and his companions began a movement of lay Catholic service to people experiencing poverty.</p>
                    </div>
                </article>
                <article class="history-timeline-item">
                    <span class="history-timeline-icon"><i class="bi bi-signpost-split" aria-hidden="true"></i></span>
                    <div class="history-timeline-card">
                        <h3>Development in Sudan</h3>
                        <p>The Society later developed in Sudan, and in 1998 its presence was established within the Diocese in Southern Sudan as part of the wider Vincentian structure.</p>
                    </div>
                </article>
                <article class="history-timeline-item">
                    <span class="history-timeline-icon"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></span>
                    <div class="history-timeline-card">
                        <h3>Growth in South Sudan</h3>
                        <p>Following the development of South Sudan, SSVP South Sudan also operates as a registered local nonprofit organization implementing humanitarian and development programmes through professional staff and project-based partnerships.</p>
                    </div>
                </article>
                <article class="history-timeline-item">
                    <span class="history-timeline-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                    <div class="history-timeline-card">
                        <h3>Our Commitment Today</h3>
                        <p>Today, the organization continues to draw inspiration from the Vincentian tradition of service, dignity, solidarity and support to vulnerable communities.</p>
                    </div>
                </article>
                <article class="history-timeline-item">
                    <span class="history-timeline-icon"><i class="bi bi-diagram-3" aria-hidden="true"></i></span>
                    <div class="history-timeline-card">
                        <h3>Our Structure</h3>
                        <p>The wider Society structure in South Sudan includes the National Council, Central Councils and Conferences serving through parishes and chapels.</p>
                    </div>
                </article>
            </div>
        </div>

        <div class="history-callout">
            <div class="history-callout-icon" aria-hidden="true"><i class="bi bi-people-fill"></i></div>
            <p>Guided by faith and compassion, rooted in the Vincentian spirit, we continue to serve and walk with the most vulnerable.</p>
        </div>
    </section>
    <section class="about-cta cta-photo-cta complete-prefooter-cta section-reveal">
        <div class="prefooter-cta-blue">
            <div class="container about-final-cta-inner cta-photo-cta-inner">
                <div class="cta-photo-cta-copy">
                    <h2 aria-label="<?php echo e($aboutPage['cta']['heading']); ?>">Together, We Can Build<br><span>Stronger</span> Communities</h2>
                    <p><?php echo e($aboutPage['cta']['text']); ?></p>
                    <div class="hero-actions">
                        <?php foreach ($aboutPage['cta']['buttons'] as $button) : ?>
                            <a class="btn <?php echo e($button['class']); ?>" href="<?php echo site_url($button['link']); ?>"><?php echo e($button['label']); ?> <i class="bi <?php echo e($button['icon']); ?>" aria-hidden="true"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <figure class="cta-photo-cta-photo">
                    <img src="<?php echo site_url('assets/images/work/our work.jpeg'); ?>" alt="SSVP South Sudan community engagement activity" loading="lazy" width="760" height="520">
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>
