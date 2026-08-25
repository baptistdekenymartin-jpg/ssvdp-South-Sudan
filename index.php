<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
$featuredActivity = ssvdp_public_featured_activity($featuredActivity);
$partners = array(
    array(
        'name' => 'The Association of the Friends of Sister Emmanuelle (ASASE)',
        'logo' => 'assets/images/partners/asase.jpeg',
        'summary' => 'Administration costs, vocational training, Be in Hope Home, clinic support, Administration Block, Guest House, poultry and agricultural farm.',
        'details' => 'Supported projects: Administration costs; Vocational Training; Be in Hope Home; Clinic; Ongoing Administration Block building; Guest House; Poultry; Agricultural Farm.'
    ),
    array(
        'name' => 'Caritas Austria &ndash; Graz-Seckau',
        'logo' => 'assets/images/partners/caritas-graz-seckau.jpeg',
        'summary' => 'SVDP Nursery & Primary School, baby feeding and partial contribution to the ongoing Administration Block construction.',
        'details' => 'Supported projects: SVDP Nursery & Primary School; Baby Feeding; partial contribution to the ongoing Administration Block construction.'
    ),
    array(
        'name' => 'Caritas Austria',
        'logo' => 'assets/images/partners/caritas-austria.png',
        'summary' => 'IDPs & Refugees Humanitarian Aid Projects.',
        'details' => 'Supported projects: IDPs & Refugees Humanitarian Aid Projects.'
    ),
    array(
        'name' => 'BBM',
        'logo' => 'assets/images/partners/bbm.jpeg',
        'summary' => 'Donation of cars and mobility support, solar power supply, SSVDP School and Health Centre in Nyarjwa.',
        'details' => 'Supported projects: Donation of cars / mobility; Solar power supply; SSVDP School; Health Centre in Nyarjwa.'
    ),
    array(
        'name' => 'missio',
        'logo' => 'assets/images/partners/missio.png',
        'summary' => 'Baby Feeding and School Feeding support.',
        'details' => 'Supported projects: Baby Feeding; School Feeding.'
    ),
    array(
        'name' => 'St Vincent de Paul Society &ndash; England and Wales',
        'logo' => 'assets/images/partners/svp-england-wales.png',
        'summary' => 'Charitable projects, IDP projects, partial contribution for Baby Feeding and annual renovation cost for Nyarjwa Clinic.',
        'details' => 'Supported projects: Charitable projects; IDP projects implemented by the SSVDP National Council; partial contribution for Baby Feeding; annual renovation cost for Nyarjwa Clinic.'
    ),
    array(
        'name' => 'PROSUDAN',
        'logo' => 'assets/images/partners/prosudan.jpeg',
        'summary' => 'Jam production, partial contribution to car/mobility donation, baby feeding, playground equipment and Administration Block support.',
        'details' => 'Supported projects: Jam production; partial contribution to car/mobility donation; Baby feeding; Playground equipment for children; partial contribution to the Administration Block.'
    )
);
$partners = ssvdp_public_partners($partners);

?>

<section class="hero-section hero-carousel section-reveal" data-hero-carousel aria-label="Homepage highlights">
    <div class="hero-slides">
        <article class="hero-slide hero-slide-first is-active" data-hero-slide aria-hidden="false">
            <div class="hero-photo" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/work/hero/slide 1.jpg'); 
?>" alt="" width="1672" height="941" onerror="this.classList.add('is-missing');">
            </div>
            <div class="container hero-grid">
                <div class="hero-copy">
                    <p class="hero-kicker">Development Through Community<br>Empowerment &amp; Participation.</p>
                    <h1 class="hero-title">Serving Communities<br>Across South Sudan</h1>
                    <p class="hero-description">We walk with the poor and vulnerable, bringing hope,<br>dignity and sustainable change to communities.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="<?php echo site_url('programmes.php'); ?>">Explore Our Work <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        <a class="btn btn-outline-light" href="<?php echo site_url('resources.php'); ?>">Get Involved <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <img class="hero-curve-logo" src="<?php echo site_url('assets/images/logo/ssvdp-logo-transparent.png'); 
?>" alt="SSVDP South Sudan" width="320" height="320">
        </article>
        <article class="hero-slide" data-hero-slide aria-hidden="true">
            <div class="hero-photo" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/work/hero/slide 2.jpg') . '?v=' . filemtime(__DIR__ . '/assets/images/work/hero/slide 2.jpg'); 
?>" alt="" width="1672" height="941" onerror="this.classList.add('is-missing');">
            </div>
        </article>
        <article class="hero-slide" data-hero-slide aria-hidden="true">
            <div class="hero-photo" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/work/hero/slide 3.jpg'); 
?>" alt="" width="1672" height="941" onerror="this.classList.add('is-missing');">
            </div>
        </article>
        <article class="hero-slide" data-hero-slide aria-hidden="true">
            <div class="hero-photo" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/work/hero/slide 4.jpg'); 
?>" alt="" width="1672" height="941" onerror="this.classList.add('is-missing');">
            </div>
        </article>
        <article class="hero-slide" data-hero-slide aria-hidden="true">
            <div class="hero-photo" aria-hidden="true">
                <img src="<?php echo site_url('assets/images/work/hero/slide 5.png'); 
?>" alt="" width="1672" height="941" onerror="this.classList.add('is-missing');">
            </div>
        </article>
    </div>
    <button class="hero-carousel-arrow hero-carousel-prev" type="button" data-hero-prev aria-label="Previous hero slide">
        <i class="bi bi-chevron-left" aria-hidden="true"></i>
    </button>
    <button class="hero-carousel-arrow hero-carousel-next" type="button" data-hero-next aria-label="Next hero slide">
        <i class="bi bi-chevron-right" aria-hidden="true"></i>
    </button>
    <div class="hero-carousel-dots" role="tablist" aria-label="Select hero slide">
        <button class="hero-carousel-dot is-active" type="button" data-hero-dot aria-label="Show slide 1" aria-selected="true"></button>
        <button class="hero-carousel-dot" type="button" data-hero-dot aria-label="Show slide 2" aria-selected="false"></button>
        <button class="hero-carousel-dot" type="button" data-hero-dot aria-label="Show slide 3" aria-selected="false"></button>
        <button class="hero-carousel-dot" type="button" data-hero-dot aria-label="Show slide 4" aria-selected="false"></button>
        <button class="hero-carousel-dot" type="button" data-hero-dot aria-label="Show slide 5" aria-selected="false"></button>
    </div>
</section>

<section class="home-motto-strip section-reveal" aria-label="SSVDP motto">
    <div class="container home-motto-strip-inner">
        <i class="bi bi-heart" aria-hidden="true"></i>
        <p>Development Through Community<br>Empowerment &amp; Participation.</p>
    </div>
</section>

<section class="content-section about-section section-reveal" id="who-we-are">
    <div class="container split-grid">
        <div class="section-copy">
            <p class="section-label"><?php echo e($about['label']); 
?></p>
            <h2><?php echo e($about['heading']); 
?></h2>
            <?php foreach ($about['paragraphs'] as $paragraph) : 
?>
                <p><?php echo e($paragraph); 
?></p>
            <?php endforeach; 
?>
            <a class="btn btn-primary" href="<?php echo site_url($about['button_link']); 
?>"><?php echo e($about['button_label']); 
?></a>
        </div>
        <div class="about-video-panel">
            <div class="about-video-frame">
                <video class="about-video" controls playsinline preload="metadata" data-preview-time="4">
                    <source src="<?php echo site_url('assets/images/work/Add a little bit of body text (1).mp4'); ?>#t=4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="about-pillars" aria-label="SSVDP approach pillars">
                <article class="about-pillar">
                    <span class="about-pillar-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                    <h3>Community Empowerment</h3>
                    <p>We equip communities with skills, knowledge and resources to build a better future.</p>
                </article>
                <article class="about-pillar">
                    <span class="about-pillar-icon"><i class="bi bi-heart-fill" aria-hidden="true"></i></span>
                    <h3>Compassionate Service</h3>
                    <p>We serve the most vulnerable with love, dignity and respect for every person.</p>
                </article>
                <article class="about-pillar">
                    <span class="about-pillar-icon"><i class="bi bi-handshake" aria-hidden="true"></i></span>
                    <h3>Partnership &amp; Collaboration</h3>
                    <p>We work together with partners and communities to create lasting and sustainable impact.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="impact-section section-reveal" id="impact" aria-labelledby="impact-heading">
    <div class="container home-impact-inner">
        <div class="home-impact-intro">
            <p class="section-label">Creating change together with communities</p>
            <h2 id="impact-heading">Our Impact</h2>
            <p>Our work is rooted in community participation, dignity and empowerment, helping people build stronger and more resilient futures.</p>
        </div>
        <div class="home-impact-grid">
            <article class="home-impact-item">
                <span class="home-impact-icon"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                <div>
                    <h3>Lives Reached</h3>
                    <p>Supporting vulnerable individuals and families through community-based initiatives.</p>
                </div>
            </article>
            <article class="home-impact-item">
                <span class="home-impact-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                <div>
                    <h3>Communities Supported</h3>
                    <p>Working directly with communities to strengthen resilience and participation.</p>
                </div>
            </article>
            <article class="home-impact-item home-impact-item--since">
                <span class="home-impact-icon"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
                <div>
                    <h3>Years of Service</h3>
                    <p>Serving communities in South Sudan <mark>Since 2009</mark>.</p>
                </div>
            </article>
            <article class="home-impact-item">
                <span class="home-impact-icon"><i class="bi bi-diagram-3" aria-hidden="true"></i></span>
                <div>
                    <h3>Local Participation</h3>
                    <p>Putting communities at the centre of identifying needs and creating sustainable solutions.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="content-section featured-activity section-reveal" id="featured-activity">
    <div class="container featured-activity-card featured-grid">
        <div class="activity-media activity-photo">
            <img src="<?php echo site_url($featuredActivity['image'] ?? 'assets/images/work/women training.jpg'); 
?>" alt="<?php echo e($featuredActivity['title']); 
?>" loading="lazy" width="560" height="430">
            <div class="activity-image-badge" aria-label="Activity focus">
                <span><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                <p>Building skills.<br>Strengthening families.<br>Empowering communities.</p>
            </div>
        </div>
        <div class="activity-report">
            <p class="activity-label"><span><i class="bi bi-star-fill" aria-hidden="true"></i></span><?php echo e($featuredActivity['label']); 
?></p>
            <h2><?php echo str_replace(array(' in ', ' and Family'), array(' in<br>', ' and<br>Family'), e($featuredActivity['title'])); 
?></h2>
            <span class="activity-title-rule" aria-hidden="true"></span>
            <dl class="activity-details">
                <div>
                    <dt><i class="bi bi-calendar3" aria-hidden="true"></i><span>Date</span></dt>
                    <dd><?php echo e($featuredActivity['date']); 
?></dd>
                </div>
                <div>
                    <dt><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Location</span></dt>
                    <dd><?php echo e($featuredActivity['location']); 
?></dd>
                </div>
                <div>
                    <dt><i class="bi bi-people" aria-hidden="true"></i><span>Participants</span></dt>
                    <dd><?php echo e($featuredActivity['participants']); 
?></dd>
                </div>
                <div>
                    <dt><i class="bi bi-tag" aria-hidden="true"></i><span>Category</span></dt>
                    <dd><?php echo e($featuredActivity['category']); 
?></dd>
                </div>
            </dl>
            <p><?php echo e($featuredActivity['excerpt']); 
?></p>
            <p class="activity-guests"><strong>Important guests:</strong> <?php echo e($featuredActivity['guests']); 
?></p>
            <a class="btn activity-report-button" href="<?php echo site_url($featuredActivity['button_link']); 
?>"><?php echo e($featuredActivity['button_label']); ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
<section class="where-section section-reveal" id="areas" aria-labelledby="where-heading">
    <div class="container">
        <div class="where-heading">
            <p class="section-label">AREA OF OPERATION</p>
            <h2 id="where-heading"><?php echo e($whereWeWork['heading']); 
?></h2>
            <p><?php echo e($whereWeWork['paragraph']); 
?></p>
        </div>

        <div class="where-layout">
            <div class="where-map-panel">
                <img src="<?php echo site_url('assets/images/placeholders/south-sudan-states.svg'); 
?>" alt="Map of South Sudan with Central Equatoria State highlighted and Juba marked" loading="lazy" width="1000" height="762">
            </div>

            <div class="where-info-panel">
                <div class="where-info-grid">
                    <article class="where-info-card">
                        <span class="where-info-icon"><i class="bi bi-bank" aria-hidden="true"></i></span>
                        <div>
                            <h3>STATE</h3>
                            <p><?php echo e($whereWeWork['state']); 
?></p>
                        </div>
                    </article>
                    <article class="where-info-card">
                        <span class="where-info-icon"><i class="bi bi-building" aria-hidden="true"></i></span>
                        <div>
                            <h3>ARCHDIOCESE</h3>
                            <p><?php echo e($whereWeWork['archdiocese']); 
?></p>
                        </div>
                    </article>
                    <article class="where-info-card">
                        <span class="where-info-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                        <div>
                            <h3>COUNTY</h3>
                            <p><?php echo e($whereWeWork['county']); 
?></p>
                        </div>
                    </article>
                    <article class="where-info-card">
                        <span class="where-info-icon"><i class="bi bi-person-hearts" aria-hidden="true"></i></span>
                        <div>
                            <h3>COMMUNITIES REACHED</h3>
                            <p><?php echo e($whereWeWork['communities_reached']); 
?></p>
                        </div>
                    </article>
                </div>

                <article class="where-office-card">
                    <span class="where-info-icon where-office-icon"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></span>
                    <div>
                        <h3>OFFICE LOCATION</h3>
                        <p><?php echo e($whereWeWork['office_location']); 
?></p>
                    </div>
                </article>
            </div>
        </div>
    </div>
    <div class="where-wave" aria-hidden="true"></div>
</section>
<section class="content-section latest-news section-reveal" id="latest-news">
    <div class="container">
        <div class="section-heading split-heading">
            <div>
                <p class="section-label">NEWS & UPDATES</p>
                <h2>Latest News and Updates</h2>
            </div>
            <a class="text-link" href="<?php echo site_url('news.php'); 
?>">View All News <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="card-grid news-grid">
            <?php foreach ($latestNews as $item) : 
?>
                <article class="news-card">
                    <div class="news-image">
                        <img src="<?php echo site_url($item['image']); 
?>" alt="<?php echo e($item['title']); 
?>" loading="lazy" width="420" height="263">
                    </div>
                    <div class="news-content">
                        <p class="news-meta"><?php echo e($item['date']); 
?> <span aria-hidden="true">|</span> <?php echo e($item['category']); 
?></p>
                        <h3><?php echo e($item['title']); 
?></h3>
                        <p><?php echo e($item['excerpt']); 
?></p>
                        <a class="text-link" href="<?php echo site_url($item['link']); 
?>">Read More <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </article>
            <?php endforeach; 
?>
        </div>
    </div>
</section>

<section class="cta-section section-reveal" id="get-involved">
    <span class="get-involved-corner" aria-hidden="true"></span>
    <span class="get-involved-dots" aria-hidden="true"></span>
    <div class="container get-involved-compact">
        <div class="get-involved-copy">
            <p class="section-label">GET INVOLVED</p>
            <h2><?php echo e($getInvolved['heading']); ?></h2>
            <p>Work with SSVDP South Sudan to support vulnerable communities through volunteering, partnership and community action.</p>
        </div>
        <div class="cta-grid" aria-label="Get involved actions">
            <article class="get-involved-action get-involved-action--blue">
                <span class="get-involved-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" focusable="false">
                        <path d="M24 39 11 27.6c-4.8-4.2-1.8-12.1 4.6-12.1 3.1 0 5.1 1.8 6.2 3.6L24 22.5l2.2-3.4c1.1-1.8 3.1-3.6 6.2-3.6 6.4 0 9.4 7.9 4.6 12.1L24 39Z" />
                        <path d="M8 23V12M40 23V12M14 15h-3a3 3 0 0 0-3 3v5M34 15h3a3 3 0 0 1 3 3v5" />
                    </svg>
                </span>
                <h3>Volunteer With Us</h3>
                <span class="get-involved-rule" aria-hidden="true"></span>
                <a class="get-involved-button get-involved-button--gold" href="<?php echo site_url('contact.php'); ?>">
                    <span>Get Involved</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            <article class="get-involved-action get-involved-action--gold">
                <span class="get-involved-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" focusable="false">
                        <path d="m19 25 5-5 6 6a4 4 0 0 0 5.7 0l2.3-2.3" />
                        <path d="m14 20 5-5 8 8" />
                        <path d="m5 25 9-9 10 10-9 9-10-10Z" />
                        <path d="m43 25-9-9-6 6 9 9 6-6Z" />
                    </svg>
                </span>
                <h3>Partner With Us</h3>
                <span class="get-involved-rule" aria-hidden="true"></span>
                <a class="get-involved-button get-involved-button--outline" href="<?php echo site_url('contact.php'); ?>">
                    <span>Explore Partnerships</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            <article class="get-involved-action get-involved-action--blue">
                <span class="get-involved-icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" focusable="false">
                        <path d="M8 14h32v22H8V14Z" />
                        <path d="m9 16 15 12 15-12" />
                    </svg>
                </span>
                <h3>Contact Our Team</h3>
                <span class="get-involved-rule" aria-hidden="true"></span>
                <a class="get-involved-button get-involved-button--outline" href="<?php echo site_url('contact.php'); ?>">
                    <span>Get In Touch</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
        </div>
    </div>
</section>
<section class="partners-section section-reveal" id="partners" aria-labelledby="partners-heading">
    <div class="container partners-inner">
        <div class="partners-heading">
            <p class="section-label">OUR PARTNERS</p>
            <h2 id="partners-heading">Our Donors &amp; Partners</h2>
            <p>Working together with committed partners to support vulnerable communities and strengthen sustainable development across South Sudan.</p>
        </div>

        <div class="partners-logo-panel">
            <div class="partners-grid" aria-label="Donor and partner logos">
            <?php foreach (array(0, 1, 2, 3, 5, 4, 6) as $partnerIndex) : $partner = $partners[$partnerIndex]; ?>
                <div class="partner-logo-item">
                    <div class="partner-logo-box">
                        <img src="<?php echo site_url($partner['logo']); ?>" alt="<?php echo e(strip_tags($partner['name'])); ?> logo" loading="lazy" width="220" height="110">
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<section class="home-prefooter-cta section-reveal" aria-labelledby="home-prefooter-cta-heading">
    <div class="home-prefooter-main">
        <div class="container home-prefooter-cta-inner">
            <p class="section-label">TOGETHER FOR STRONGER COMMUNITIES</p>
            <h2 id="home-prefooter-cta-heading">Empowering communities.<br>Restoring dignity.<br>Building hope.</h2>
            <p>Through community participation and collective action, SSVDP South Sudan works alongside people to create opportunities for a stronger and more sustainable future.</p>
            <a class="btn home-prefooter-button" href="<?php echo site_url('programmes.php'); 
?>">Discover Our Work <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="home-values-strip">
        <div class="container home-values-grid">
            <article class="home-value-item">
                <span class="home-value-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                <h3>Community Centered</h3>
                <p>We put communities at the heart of everything we do.</p>
            </article>
            <article class="home-value-item">
                <span class="home-value-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                <h3>Compassion in Action</h3>
                <p>We serve with love and stand with the vulnerable.</p>
            </article>
            <article class="home-value-item">
                <span class="home-value-icon"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></span>
                <h3>Sustainable Impact</h3>
                <p>We promote lasting change through empowerment.</p>
            </article>
            <article class="home-value-item">
                <span class="home-value-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></span>
                <h3>Dignity for All</h3>
                <p>We uphold the dignity and rights of every person.</p>
            </article>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; 
?>


