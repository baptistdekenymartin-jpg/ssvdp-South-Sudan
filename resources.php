<?php
$pageTitle = 'Get Involved';
$pageDescription = 'Volunteer, partner with, or support the mission of SSVP South Sudan.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/public-submissions.php';
$getInvolvedResult = ssvdp_handle_get_involved_submission();
?>

<main class="get-involved-page">
    <section class="get-involved-hero section-reveal">
        <div class="get-involved-hero-shape get-involved-hero-shape-one" aria-hidden="true"></div>
        <div class="get-involved-hero-shape get-involved-hero-shape-two" aria-hidden="true"></div>
        <div class="get-involved-hero-dots" aria-hidden="true"></div>
        <div class="container get-involved-hero-inner">
            <div class="get-involved-hero-copy">
                <p class="section-label">GET INVOLVED</p>
                <h1>Be Part of Something Meaningful</h1>
                <p>Whether you give your time, share your expertise or work alongside us as a partner, your involvement can help strengthen communities and create lasting positive change.</p>
                <div class="hero-actions get-involved-hero-actions">
                    <a class="btn btn-primary" href="#volunteer">Volunteer With Us <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    <a class="btn btn-outline-light" href="#partner">Partner With Us <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="get-involved-intro section-reveal" aria-labelledby="get-involved-intro-heading">
        <div class="container get-involved-intro-inner">
            <h2 id="get-involved-intro-heading">Together, We Can Make a Difference</h2>
            <p>SSVP South Sudan believes meaningful change happens through participation, solidarity and partnership. There are many ways individuals, organizations and communities can contribute to our mission.</p>
        </div>
    </section>

    <section class="involvement-options section-reveal" aria-label="Ways to get involved">
        <div class="container involvement-card-grid">
            <article class="involvement-card" id="volunteer">
                <span class="involvement-card-icon involvement-card-icon-blue"><i class="bi bi-people" aria-hidden="true"></i></span>
                <h2>Volunteer With Us</h2>
                <span class="involvement-card-rule" aria-hidden="true"></span>
                <p>Share your time, skills and experience to support activities that strengthen individuals, families and communities.</p>
                <a class="btn btn-secondary-dark" href="<?php echo site_url('contact.php'); ?>">Become a Volunteer</a>
            </article>
            <article class="involvement-card" id="partner">
                <span class="involvement-card-icon involvement-card-icon-gold"><i class="bi bi-handshake" aria-hidden="true"></i></span>
                <h2>Partner With Us</h2>
                <span class="involvement-card-rule" aria-hidden="true"></span>
                <p>Work with SSVP South Sudan to strengthen community-led initiatives and create sustainable impact together.</p>
                <a class="btn btn-secondary-dark" href="<?php echo site_url('contact.php'); ?>">Explore Partnership</a>
            </article>
            <article class="involvement-card" id="support">
                <span class="involvement-card-icon involvement-card-icon-blue"><i class="bi bi-heart" aria-hidden="true"></i></span>
                <h2>Support Our Mission</h2>
                <span class="involvement-card-rule" aria-hidden="true"></span>
                <p>Help strengthen the work of SSVP South Sudan by supporting initiatives that promote dignity, resilience and opportunity.</p>
                <a class="btn btn-secondary-dark" href="#why-involved">Learn More</a>
            </article>
        </div>
    </section>

    <section class="why-involved-section section-reveal" id="why-involved">
        <div class="container why-involved-grid">
            <div class="why-involved-copy">
                <p class="section-label">WHY GET INVOLVED</p>
                <h2>Why Your Involvement Matters</h2>
                <p>Every contribution matters. By working together, we can reach more people, strengthen local capacity and support communities to shape their own future.</p>
            </div>
            <div class="why-involved-list" aria-label="Reasons your involvement matters">
                <article class="why-involved-point">
                    <span><i class="bi bi-diagram-3" aria-hidden="true"></i></span>
                    <div>
                        <h3>Community-Led</h3>
                        <p>Local voices help guide priorities, action and sustainable solutions.</p>
                    </div>
                </article>
                <article class="why-involved-point">
                    <span><i class="bi bi-person-heart" aria-hidden="true"></i></span>
                    <div>
                        <h3>People First</h3>
                        <p>Our work protects dignity and responds to real human needs.</p>
                    </div>
                </article>
                <article class="why-involved-point">
                    <span><i class="bi bi-tree" aria-hidden="true"></i></span>
                    <div>
                        <h3>Lasting Impact</h3>
                        <p>Shared effort builds resilience, skills and long-term community strength.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>


    <section class="get-involved-form-section section-reveal" aria-labelledby="get-involved-form-heading">
        <div class="container">
            <div class="get-involved-form-card">
                <p class="section-label">SUBMIT YOUR INTEREST</p>
                <h2 id="get-involved-form-heading">Get Involved With SSVP South Sudan</h2>
                <p>Share your interest with our team and we will follow up on the next steps.</p>
                <form id="get-involved-submission-form" class="contact-form" action="<?php echo site_url('resources.php'); ?>#get-involved-submission-form" method="post">
                    <input type="hidden" name="form_type" value="get_involved">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="started_at" value="<?php echo e((string) time()); ?>">
                    <div class="form-honeypot" aria-hidden="true"><label for="involved-website">Website</label><input type="text" id="involved-website" name="website" tabindex="-1" autocomplete="off"></div>
                    <?php if ($getInvolvedResult['success']) : ?><div class="form-alert form-alert--success"><?php echo e($getInvolvedResult['success']); ?></div><?php endif; ?>
                    <?php foreach ($getInvolvedResult['errors'] as $error) : ?><div class="form-alert form-alert--error"><?php echo e($error); ?></div><?php endforeach; ?>
                    <div class="form-field"><label for="involved-name">Full Name</label><input type="text" id="involved-name" name="full_name" autocomplete="name" value="<?php echo ssvdp_form_old('full_name', $getInvolvedResult['values']); ?>" required></div>
                    <div class="form-field"><label for="involved-email">Email Address</label><input type="email" id="involved-email" name="email" autocomplete="email" value="<?php echo ssvdp_form_old('email', $getInvolvedResult['values']); ?>" required></div>
                    <div class="form-field"><label for="involved-phone">Phone Number</label><input type="tel" id="involved-phone" name="phone" autocomplete="tel" value="<?php echo ssvdp_form_old('phone', $getInvolvedResult['values']); ?>"></div>
                    <div class="form-field"><label for="involved-location">Location</label><input type="text" id="involved-location" name="location" value="<?php echo ssvdp_form_old('location', $getInvolvedResult['values']); ?>"></div>
                    <div class="form-field"><label for="involved-type">I want to get involved as</label><select id="involved-type" name="involvement_type" required><option value="">Select one</option><?php foreach (array('Volunteer','Partner','Donor','Community Member','Other') as $option) : ?><option value="<?php echo e($option); ?>" <?php echo ($getInvolvedResult['values']['involvement_type'] ?? '') === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option><?php endforeach; ?></select></div>
                    <div class="form-field form-field-full"><label>Areas of Interest</label><div class="get-involved-interest-grid"><?php foreach (array('Volunteering','Partnerships','Livelihoods','Education','Healthcare','Emergency Support') as $area) : ?><label><input type="checkbox" name="areas_of_interest[]" value="<?php echo e($area); ?>" <?php echo in_array($area, $getInvolvedResult['values']['areas_of_interest'] ?? array(), true) ? 'checked' : ''; ?>> <?php echo e($area); ?></label><?php endforeach; ?></div></div>
                    <div class="form-field form-field-full"><label for="involved-message">Message</label><textarea id="involved-message" name="message" rows="5"><?php echo ssvdp_form_old('message', $getInvolvedResult['values']); ?></textarea></div>
                    <button class="btn btn-primary contact-submit" type="submit">Submit Interest <i class="bi bi-send" aria-hidden="true"></i></button>
                </form>
            </div>
        </div>
    </section>
    <section class="get-involved-final-cta section-reveal" aria-labelledby="get-involved-final-heading">
        <div class="container get-involved-final-cta-inner">
            <p class="section-label">READY TO GET INVOLVED?</p>
            <h2 id="get-involved-final-heading">Your contribution can help build stronger communities.</h2>
            <p>Connect with SSVP South Sudan and discover how you can contribute your time, experience or partnership.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?php echo site_url('contact.php'); ?>">Contact Us <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
