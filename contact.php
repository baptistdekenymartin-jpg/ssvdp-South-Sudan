<?php
$pageTitle = 'Contact Us';
$pageDescription = 'Contact Society of St. Vincent de Paul South Sudan for programme, partnership and community enquiries.';
$assetVersion = 'contact-page-v1';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/public-submissions.php';
$contactResult = ssvdp_handle_contact_submission();

$primaryEmail = $contactInformation['emails'][0] ?? 'Email address to be confirmed';
?>

<div class="contact-page">
    <section class="contact-hero section-reveal">
        <div class="container contact-hero-inner">
            <p class="section-label">CONTACT SSVP</p>
            <h1>Contact Us</h1>
            <p>We are here to connect, collaborate and serve communities across South Sudan.</p>
        </div>
    </section>

    <section class="contact-section section-reveal" aria-labelledby="contact-section-heading">
        <div class="container contact-grid">
            <div class="contact-info-card">
                <p class="section-label">REACH OUR OFFICE</p>
                <h2 id="contact-section-heading">Society of St. Vincent de Paul South Sudan</h2>
                <p class="contact-intro">Connect with our team for programme information, partnership enquiries and community engagement.</p>

                <div class="contact-info-list" aria-label="Organization contact information">
                    <article class="contact-info-item">
                        <span><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                        <div>
                            <h3>Office Location</h3>
                            <p><?php echo e($contactInformation['office'] ?? 'Office location to be confirmed'); ?></p>
                        </div>
                    </article>
                    <article class="contact-info-item">
                        <span><i class="bi bi-telephone" aria-hidden="true"></i></span>
                        <div>
                            <h3>Phone Number</h3>
                            <?php if (!empty($contactInformation['telephone']) && !empty($contactInformation['telephone_link'])) : ?>
                                <p><a href="<?php echo e($contactInformation['telephone_link']); ?>"><?php echo e($contactInformation['telephone']); ?></a></p>
                            <?php else : ?>
                                <p>Phone number to be confirmed</p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <article class="contact-info-item">
                        <span><i class="bi bi-envelope" aria-hidden="true"></i></span>
                        <div>
                            <h3>Email Address</h3>
                            <?php if (!empty($primaryEmail)) : ?>
                                <p><a href="mailto:<?php echo e($primaryEmail); ?>"><?php echo e($primaryEmail); ?></a></p>
                            <?php else : ?>
                                <p>Email address to be confirmed</p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <article class="contact-info-item">
                        <span><i class="bi bi-clock" aria-hidden="true"></i></span>
                        <div>
                            <h3>Office Hours</h3>
                            <p><?php echo e($contactInformation['office_schedule'] ?? 'Office hours to be confirmed'); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <div class="contact-form-card">
                <p class="section-label">SEND A MESSAGE</p>
                <h2>How can we help?</h2>
                <form class="contact-form" action="<?php echo site_url('contact.php'); ?>" method="post">
                    <input type="hidden" name="form_type" value="contact">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="started_at" value="<?php echo e((string) time()); ?>">
                    <div class="form-honeypot" aria-hidden="true"><label for="contact-website">Website</label><input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off"></div>
                    <?php if ($contactResult['success']) : ?><div class="form-alert form-alert--success"><?php echo e($contactResult['success']); ?></div><?php endif; ?>
                    <?php foreach ($contactResult['errors'] as $error) : ?><div class="form-alert form-alert--error"><?php echo e($error); ?></div><?php endforeach; ?>
                    <div class="form-field">
                        <label for="contact-name">Full Name</label>
                        <input type="text" id="contact-name" name="name" autocomplete="name" value="<?php echo ssvdp_form_old('name', $contactResult['values']); ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="contact-email">Email Address</label>
                        <input type="email" id="contact-email" name="email" autocomplete="email" value="<?php echo ssvdp_form_old('email', $contactResult['values']); ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="contact-phone">Phone Number</label>
                        <input type="tel" id="contact-phone" name="phone" autocomplete="tel" value="<?php echo ssvdp_form_old('phone', $contactResult['values']); ?>">
                    </div>
                    <div class="form-field">
                        <label for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" name="subject" value="<?php echo ssvdp_form_old('subject', $contactResult['values']); ?>" required>
                    </div>
                    <div class="form-field form-field-full">
                        <label for="contact-message">Message</label>
                        <textarea id="contact-message" name="message" rows="6" required><?php echo ssvdp_form_old('message', $contactResult['values']); ?></textarea>
                    </div>
                    <button class="btn btn-primary contact-submit" type="submit">Send Message <i class="bi bi-send" aria-hidden="true"></i></button>
                </form>
            </div>
        </div>
    </section>

    <section class="contact-map-section section-reveal" aria-labelledby="contact-map-heading">
        <div class="container">
            <div class="contact-map-panel">
                <div class="contact-map-copy">
                    <p class="section-label">VISIT OR REACH US</p>
                    <h2 id="contact-map-heading">Find SSVP South Sudan</h2>
                    <p>Confirmed map details can be added here when the official Google Maps embed is available.</p>
                </div>
                <div class="contact-map-placeholder" role="img" aria-label="Google Maps embed placeholder for SSVP South Sudan office location">
                    <i class="bi bi-map" aria-hidden="true"></i>
                    <span>Google Maps embed ready</span>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-final-cta section-reveal" aria-labelledby="contact-final-heading">
        <div class="container contact-final-cta-inner">
            <p class="section-label">TOGETHER, WE CAN MAKE A DIFFERENCE</p>
            <h2 id="contact-final-heading">Connect with SSVP South Sudan to learn more about our work, partnerships and community programmes.</h2>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?php echo site_url('resources.php'); ?>">Get Involved <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
