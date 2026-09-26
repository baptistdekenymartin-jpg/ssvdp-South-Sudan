<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
$adminTitle = 'Security';
$activeNav = 'security';
function security_count(PDO $pdo, string $sql): int { try { return (int) $pdo->query($sql)->fetchColumn(); } catch (Throwable $e) { return 0; } }
function security_latest_activity(PDO $pdo): string
{
    if (!ssvdp_table_exists($pdo, 'admin_activity_log')) { return 'No activity log table'; }
    try {
        $stmt = $pdo->query("SELECT action, entity_type, created_at FROM admin_activity_log WHERE action IN ('login','logout','created','updated','deleted','published','unpublished','archived','exported') ORDER BY created_at DESC, id DESC LIMIT 1");
        $row = $stmt ? $stmt->fetch() : null;
        if (!$row) { return 'No security activity recorded yet'; }
        $time = strtotime((string) $row['created_at']);
        $when = $time ? date('j M Y, g:i A', $time) : (string) $row['created_at'];
        return ucfirst((string) $row['action']) . ' / ' . (string) $row['entity_type'] . ' at ' . $when;
    } catch (Throwable $e) {
        return 'Activity log unavailable';
    }
}
$https = ssvdp_is_https_request();
$cookieParams = session_get_cookie_params();
$adminCount = security_count($pdo, "SELECT COUNT(*) FROM admin_users WHERE role = 'administrator' AND status = 'active'");
$editorCount = security_count($pdo, "SELECT COUNT(*) FROM admin_users WHERE role = 'editor' AND status = 'active'");
$reviewerCount = security_count($pdo, "SELECT COUNT(*) FROM admin_users WHERE role = 'reviewer' AND status = 'active'");
$failedRecent = security_count($pdo, "SELECT COUNT(*) FROM admin_login_attempts WHERE success = 0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
$uploadHtaccess = is_file(dirname(__DIR__, 2) . '/uploads/.htaccess');
$twoFaReady = ssvdp_table_exists($pdo, 'admin_users');
$latestActivity = security_latest_activity($pdo);
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><h2>Admin Security Status</h2><div class="admin-detail-grid"><div class="admin-detail-item"><span>Production Mode</span><?php echo ssvdp_is_production() ? 'Enabled' : 'Development / localhost mode'; ?></div><div class="admin-detail-item"><span>HTTPS</span><?php echo $https ? 'Detected' : 'Not detected on this request'; ?></div><div class="admin-detail-item"><span>Secure Session Cookie</span><?php echo !empty($cookieParams['secure']) ? 'Secure flag active' : 'Secure flag inactive until HTTPS is used'; ?>, HttpOnly, SameSite <?php echo e((string) ($cookieParams['samesite'] ?? 'Lax')); ?></div><div class="admin-detail-item"><span>Session Lifetime</span>Idle timeout <?php echo (int) (ADMIN_IDLE_TIMEOUT / 60); ?> minutes, absolute lifetime <?php echo (int) (ADMIN_ABSOLUTE_TIMEOUT / 3600); ?> hours</div><div class="admin-detail-item"><span>CSRF Protection</span>Enabled for admin POST actions</div><div class="admin-detail-item"><span>Failed Login Protection</span>Progressive cooldown after repeated account/IP failures</div><div class="admin-detail-item"><span>Upload Protection</span><?php echo $uploadHtaccess ? 'MIME checks plus upload .htaccess present' : 'MIME checks active; upload .htaccess missing'; ?></div><div class="admin-detail-item"><span>PHP display_errors</span><?php echo ini_get('display_errors') ? 'On - disable for production' : 'Off'; ?></div><div class="admin-detail-item"><span>Administrator Accounts</span><?php echo e((string) $adminCount); ?> active</div><div class="admin-detail-item"><span>Editor Accounts</span><?php echo e((string) $editorCount); ?> active</div><div class="admin-detail-item"><span>Reviewer Accounts</span><?php echo e((string) $reviewerCount); ?> active</div><div class="admin-detail-item"><span>Recent Failed Login Attempts</span><?php echo e((string) $failedRecent); ?></div><div class="admin-detail-item"><span>2FA Structure</span><?php echo $twoFaReady ? 'Database fields ready; 2FA not enabled' : 'Not ready'; ?></div><div class="admin-detail-item"><span>Latest Security Activity</span><?php echo e($latestActivity); ?></div></div></section><section class="admin-panel" style="margin-top:22px"><h2>Backup / Recovery Guidance</h2><p class="admin-muted">For localhost, use phpMyAdmin export for the `ssvdp_south_sudan` database and keep uploads backed up separately. For production, use scheduled hosting/database backups and store backup files outside the public web root. See docs/production-security-checklist.md before publishing.</p></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
