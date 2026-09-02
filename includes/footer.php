    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <section class="footer-column footer-organization" aria-label="SSVP South Sudan">
                <a class="brand footer-brand" href="<?php echo site_url('index.php'); ?>">
                    <img src="<?php echo site_url($siteConfig['logo']); ?>" alt="Society of St. Vincent de Paul South Sudan logo" class="site-logo" width="96" height="96" loading="lazy">
                    <span>
                        <strong>SSVP South Sudan</strong>
                        <small>Serviens in Spe</small>
                    </span>
                </a>
                <p><?php echo e($siteConfig['site_description']); ?></p>
                <div class="social-links footer-social-links" aria-label="Social and contact links">
                    <a href="<?php echo e($contactInformation['facebook']); ?>" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    <a href="<?php echo !empty($contactInformation['whatsapp_url']) ? e($contactInformation['whatsapp_url']) : site_url('contact.php'); ?>" aria-label="WhatsApp" <?php echo !empty($contactInformation['whatsapp_url']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                    <a href="<?php echo site_url('contact.php'); ?>" aria-label="Email"><i class="bi bi-envelope-fill" aria-hidden="true"></i></a>
                </div>
            </section>
            <nav class="footer-column" aria-label="Footer quick links">
                <h3>Quick Links</h3>
                <ul class="footer-link-list">
                    <li><a href="<?php echo site_url('index.php'); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i>Home</a></li>
                    <li><a href="<?php echo site_url('about.php'); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i>About SSVP</a></li>
                    <li><a href="<?php echo site_url('news.php'); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i>News &amp; Updates</a></li>
                    <li><a href="<?php echo site_url('gallery.php'); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i>Gallery</a></li>
                    <li><a href="<?php echo site_url('resources.php'); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i>Get Involved</a></li>
                    <li><a href="<?php echo site_url('contact.php'); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i>Contact Us</a></li>
                </ul>
            </nav>
            <section class="footer-column" aria-labelledby="footer-programmes-heading">
                <h3 id="footer-programmes-heading">Our Programmes</h3>
                <ul class="footer-link-list footer-programmes-list">
                    <li><a href="<?php echo site_url('programme.php?programme=vocational-training'); ?>">Vocational Training</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=education'); ?>">Education</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=healthcare'); ?>">Healthcare Services</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=child-care'); ?>">Child Protection, Rehabilitation and Re-integration</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=food-nutrition'); ?>">Nutrition</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=agriculture-livelihoods'); ?>">Agriculture and Livelihoods</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=humanitarian-assistance'); ?>">Humanitarian Assistance</a></li>
                    <li><a href="<?php echo site_url('programme.php?programme=community-development'); ?>">Self-Reliance Initiative</a></li>
                </ul>
            </section>
            <section class="footer-column" aria-labelledby="footer-contact-heading">
                <h3 id="footer-contact-heading">Contact Us</h3>
                <ul class="footer-contact-list">
                    <li><i class="bi bi-telephone" aria-hidden="true"></i><span><strong>Phone:</strong> <?php echo e($contactInformation['telephone']); ?></span></li>

                    <?php foreach ($contactInformation['emails'] as $email) : ?>
                        <li><i class="bi bi-envelope" aria-hidden="true"></i><span><strong>Email:</strong> <?php echo e($email); ?></span></li>
                    <?php endforeach; ?>
                    <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span><strong>Office:</strong> <?php echo e($contactInformation['office']); ?></span></li>
                    <li><i class="bi bi-clock" aria-hidden="true"></i><span><strong>Office schedule:</strong> <?php echo e($contactInformation['office_schedule']); ?></span></li>
                </ul>
            </section>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p>&copy; 2026 Society of St. Vincent de Paul South Sudan. All Rights Reserved.</p>
                <div class="footer-bottom-right">
                    <span>Serviens in Spe</span>
                    <div class="social-links footer-bottom-social" aria-label="Footer social links">
                        <a href="<?php echo e($contactInformation['facebook']); ?>" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                        <a href="<?php echo !empty($contactInformation['whatsapp_url']) ? e($contactInformation['whatsapp_url']) : site_url('contact.php'); ?>" aria-label="WhatsApp" <?php echo !empty($contactInformation['whatsapp_url']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                        <a href="<?php echo site_url('contact.php'); ?>" aria-label="Email"><i class="bi bi-envelope-fill" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?php if (!empty($contactInformation['whatsapp_url'])) : ?>
        <a class="floating-whatsapp" href="<?php echo e($contactInformation['whatsapp_url']); ?>" aria-label="Contact SSVP on WhatsApp" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-whatsapp" aria-hidden="true"></i>
        </a>
    <?php endif; ?>
    <button class="back-to-top" type="button" aria-label="Back to top">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <script src="<?php echo site_url('assets/js/main.js') . '?v=' . rawurlencode((string) filemtime(__DIR__ . '/../assets/js/main.js')); ?>"></script>
</body>
</html>
