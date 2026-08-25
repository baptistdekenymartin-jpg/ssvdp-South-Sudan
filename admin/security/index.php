<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
$adminTitle = 'Security';
$activeNav = 'security';
function security_count(PDO $pdo, string $sql): int { try { return (int) $pdo->query($sql)->fetchColumn(); } catch (Throwable $e) { return 0; } }
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$adminCount = security_count($pdo, "SELECT COUNT(*) FROM admin_users WHERE role = 'administrator' AND status = 'active'");
$editorCount = security_count($pdo, "SELECT COUNT(*) FROM admin_users WHERE role = 'editor' AND status = 'active'");
$failedRecent = security_count($pdo, "SELECT COUNT(*) FROM admin_login_attempts WHERE success = 0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
$twoFaReady = ssvdp_table_exists($pdo, 'admin_users');
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><h2>Admin Security Status</h2><div class="admin-detail-grid"><div class="admin-detail-item"><span>HTTPS</span><?php echo $https ? 'Detected' : 'Not detected on this request'; ?></div><div class="admin-detail-item"><span>Session Security</span>Idle timeout <?php echo (int) (ADMIN_IDLE_TIMEOUT / 60); ?> minutes, HttpOnly/SameSite cookies</div><div class="admin-detail-item"><span>CSRF Protection</span>Enabled for admin POST actions</div><div class="admin-detail-item"><span>Failed Login Protection</span>5 failed attempts / 15 minutes</div><div class="admin-detail-item"><span>Upload Validation</span>Enabled: MIME, extension, image validation and size limits</div><div class="admin-detail-item"><span>Administrator Accounts</span><?php echo e((string) $adminCount); ?> active</div><div class="admin-detail-item"><span>Editor Accounts</span><?php echo e((string) $editorCount); ?> active</div><div class="admin-detail-item"><span>Recent Failed Login Attempts</span><?php echo e((string) $failedRecent); ?></div><div class="admin-detail-item"><span>2FA Structure</span><?php echo $twoFaReady ? 'Database fields ready; 2FA not enabled' : 'Not ready'; ?></div></div></section><section class="admin-panel" style="margin-top:22px"><h2>Backup / Recovery Guidance</h2><p class="admin-muted">For localhost, use phpMyAdmin export for the `ssvdp_south_sudan` database and keep uploads backed up separately. For production, use scheduled hosting/database backups and store backup files outside the public web root.</p></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>