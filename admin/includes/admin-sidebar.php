<?php require_once __DIR__ . '/communication.php'; ?>
<aside class="admin-sidebar" data-admin-sidebar>
    <a class="admin-brand" href="<?php echo admin_url('dashboard.php'); ?>">
        <img src="<?php echo site_url('assets/images/logo/ssvdp-logo-cutout.png'); ?>" alt="SSVDP South Sudan" width="56" height="56">
        <span><strong>SSVDP South Sudan</strong><small>Serviens in Spe</small></span>
    </a>
    <nav class="admin-nav" aria-label="Admin navigation">
        <p>DASHBOARD</p>
        <a class="<?php echo $activeNav === 'dashboard' ? 'is-active' : ''; ?>" href="<?php echo admin_url('dashboard.php'); ?>"><i class="bi bi-speedometer2" aria-hidden="true"></i> Dashboard</a>
        <p>CONTENT MANAGEMENT</p>
        <a class="<?php echo $activeNav === 'news' ? 'is-active' : ''; ?>" href="<?php echo admin_url('news/'); ?>"><i class="bi bi-newspaper" aria-hidden="true"></i> News &amp; Updates</a>
        <a class="<?php echo $activeNav === 'featured' ? 'is-active' : ''; ?>" href="<?php echo admin_url('featured-activity/'); ?>"><i class="bi bi-star" aria-hidden="true"></i> Featured Activity</a>
        <a class="<?php echo $activeNav === 'gallery' ? 'is-active' : ''; ?>" href="<?php echo admin_url('gallery/'); ?>"><i class="bi bi-images" aria-hidden="true"></i> Gallery</a>
        <a class="<?php echo $activeNav === 'events' ? 'is-active' : ''; ?>" href="<?php echo admin_url('events/'); ?>"><i class="bi bi-calendar-event" aria-hidden="true"></i> Events &amp; Announcements</a>
        <a class="<?php echo $activeNav === 'programme-updates' ? 'is-active' : ''; ?>" href="<?php echo admin_url('programme-updates/'); ?>"><i class="bi bi-clipboard-data" aria-hidden="true"></i> Programme Updates</a>
        <a class="<?php echo $activeNav === 'impact' ? 'is-active' : ''; ?>" href="<?php echo admin_url('impact/'); ?>"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Impact Updates</a>
        <a class="<?php echo $activeNav === 'documents' ? 'is-active' : ''; ?>" href="<?php echo admin_url('documents/'); ?>"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Documents / Resources</a>
        <a class="<?php echo $activeNav === 'partners' ? 'is-active' : ''; ?>" href="<?php echo admin_url('partners/'); ?>"><i class="bi bi-handshake" aria-hidden="true"></i> Partners &amp; Donors</a>
        <p>COMMUNICATION</p>
        <a class="<?php echo $activeNav === 'messages' ? 'is-active' : ''; ?>" href="<?php echo admin_url('messages/'); ?>"><i class="bi bi-envelope" aria-hidden="true"></i> Messages / Enquiries <?php echo admin_phase3_badge(admin_phase3_count('new_messages')); ?></a>
        <a class="<?php echo $activeNav === 'get-involved' ? 'is-active' : ''; ?>" href="<?php echo admin_url('get-involved/'); ?>"><i class="bi bi-heart" aria-hidden="true"></i> Get Involved Requests <?php echo admin_phase3_badge(admin_phase3_count('new_involved')); ?></a>
        <a class="<?php echo $activeNav === 'newsletter' ? 'is-active' : ''; ?>" href="<?php echo admin_url('newsletter/'); ?>"><i class="bi bi-envelope-paper" aria-hidden="true"></i> Newsletter Subscribers</a>
        <?php if (admin_is_administrator()) : ?>
        <p>SYSTEM</p>
        <a class="<?php echo $activeNav === 'users' ? 'is-active' : ''; ?>" href="<?php echo admin_url('users/'); ?>"><i class="bi bi-people" aria-hidden="true"></i> Staff Users</a>
        <a class="<?php echo $activeNav === 'activity-log' ? 'is-active' : ''; ?>" href="<?php echo admin_url('activity-log/'); ?>"><i class="bi bi-list-check" aria-hidden="true"></i> Activity Log</a>
        <a class="<?php echo $activeNav === 'security' ? 'is-active' : ''; ?>" href="<?php echo admin_url('security/'); ?>"><i class="bi bi-shield-lock" aria-hidden="true"></i> Security</a>
        <?php endif; ?>
    </nav>
    <a class="admin-logout" href="<?php echo admin_url('logout.php'); ?>"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout</a>
</aside>

