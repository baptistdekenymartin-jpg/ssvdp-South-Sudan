<?php
require_once __DIR__ . '/includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$adminTitle = 'Dashboard';
$activeNav = 'dashboard';

function dashboard_count(PDO $pdo, string $sql): int { try { return (int) $pdo->query($sql)->fetchColumn(); } catch (Throwable $e) { return 0; } }
$publishedNews = dashboard_count($pdo, "SELECT COUNT(*) FROM news WHERE status = 'published' AND archived_at IS NULL");
$draftNews = dashboard_count($pdo, "SELECT COUNT(*) FROM news WHERE status = 'draft' AND archived_at IS NULL");
$galleryPhotos = dashboard_count($pdo, "SELECT COUNT(*) FROM gallery_photos p INNER JOIN gallery_albums a ON a.id = p.album_id WHERE a.status = 'published'");
$featuredActive = dashboard_count($pdo, "SELECT COUNT(*) FROM featured_activity WHERE status = 'active'") > 0 ? 'Active' : 'Not Set';
$upcomingEvents = dashboard_count($pdo, "SELECT COUNT(*) FROM events WHERE status = 'published' AND start_date >= CURDATE()");
$publishedProgrammeUpdates = dashboard_count($pdo, "SELECT COUNT(*) FROM programme_updates WHERE status = 'published'");
$activePartners = dashboard_count($pdo, "SELECT COUNT(*) FROM partners WHERE status = 'active'");
$publishedDocuments = dashboard_count($pdo, "SELECT COUNT(*) FROM documents WHERE status = 'published'");
$newContactMessages = dashboard_count($pdo, "SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
$newGetInvolvedRequests = dashboard_count($pdo, "SELECT COUNT(*) FROM get_involved_submissions WHERE status = 'new'");
$activeNewsletterSubscribers = dashboard_count($pdo, "SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'");
$recent = array();
try { $recent = $pdo->query('SELECT description, created_at FROM admin_activity_log ORDER BY created_at DESC LIMIT 8')->fetchAll(); } catch (Throwable $e) {}
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
require __DIR__ . '/includes/admin-header.php';
?>
<section class="admin-greeting">
    <h2><?php echo e($greeting); ?>, SSVP Team!</h2>
    <p>Here&apos;s what&apos;s happening on your website.</p>
</section>
<section class="admin-stats" aria-label="Dashboard statistics">
    <article class="admin-stat"><i class="bi bi-newspaper" aria-hidden="true"></i><div><span>Published News</span><strong><?php echo e((string) $publishedNews); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-pencil-square" aria-hidden="true"></i><div><span>Draft News</span><strong><?php echo e((string) $draftNews); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-images" aria-hidden="true"></i><div><span>Gallery Photos</span><strong><?php echo e((string) $galleryPhotos); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-star" aria-hidden="true"></i><div><span>Featured Activity</span><strong><?php echo e($featuredActive); ?></strong></div></article>
</section>
<section class="admin-stats" aria-label="Phase 2 content status">
    <article class="admin-stat"><i class="bi bi-calendar-event" aria-hidden="true"></i><div><span>Upcoming Events</span><strong><?php echo e((string) $upcomingEvents); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-clipboard-data" aria-hidden="true"></i><div><span>Programme Updates</span><strong><?php echo e((string) $publishedProgrammeUpdates); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-handshake" aria-hidden="true"></i><div><span>Active Partners</span><strong><?php echo e((string) $activePartners); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><div><span>Published Documents</span><strong><?php echo e((string) $publishedDocuments); ?></strong></div></article>
</section>
<section class="admin-stats" aria-label="Communication status">
    <article class="admin-stat"><i class="bi bi-envelope" aria-hidden="true"></i><div><span>New Messages</span><strong><?php echo e((string) $newContactMessages); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-heart" aria-hidden="true"></i><div><span>New Get Involved</span><strong><?php echo e((string) $newGetInvolvedRequests); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-envelope-paper" aria-hidden="true"></i><div><span>Active Subscribers</span><strong><?php echo e((string) $activeNewsletterSubscribers); ?></strong></div></article>
    <article class="admin-stat"><i class="bi bi-bell" aria-hidden="true"></i><div><span>Needs Attention</span><strong><?php echo e((string) ($newContactMessages + $newGetInvolvedRequests)); ?></strong></div></article>
</section>
<section class="admin-grid-2">
    <article class="admin-panel">
        <h2>Quick Actions</h2>
        <div class="admin-actions">
            <a class="admin-button" href="<?php echo admin_url('news/add.php'); ?>"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add News</a>
            <a class="admin-button admin-button--yellow" href="<?php echo admin_url('gallery/'); ?>"><i class="bi bi-upload" aria-hidden="true"></i> Upload Photos</a>
            <a class="admin-button admin-button--light" href="<?php echo admin_url('featured-activity/'); ?>"><i class="bi bi-star" aria-hidden="true"></i> Update Featured Activity</a>
            <a class="admin-button" href="<?php echo admin_url('events/add.php'); ?>"><i class="bi bi-calendar-plus" aria-hidden="true"></i> Add Event</a>
            <a class="admin-button admin-button--yellow" href="<?php echo admin_url('programme-updates/add.php'); ?>"><i class="bi bi-plus-circle" aria-hidden="true"></i> Programme Update</a>
        </div>
    </article>
    <article class="admin-panel">
        <h2>Recent Activity</h2>
        <?php if ($recent) : ?>
            <div class="admin-form">
                <?php foreach ($recent as $item) : ?>
                    <p style="margin:0"><strong><?php echo e($item['description']); ?></strong><br><small class="admin-muted"><?php echo e(ssvdp_format_date($item['created_at'])); ?></small></p>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="admin-muted">No admin activity has been recorded yet.</p>
        <?php endif; ?>
    </article>
</section>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
