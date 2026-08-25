<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$adminTitle = 'Change Password';
$activeNav = 'account';
$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    $stmt = $pdo->prepare('SELECT password_hash FROM admin_users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $adminUser['id']]);
    $hash = (string) $stmt->fetchColumn();
    if (!password_verify($current, $hash)) { $errors[] = 'Current password is incorrect.'; }
    if ($new !== $confirm) { $errors[] = 'New passwords do not match.'; }
    $errors = array_merge($errors, admin_password_errors($new));
    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ?, password_changed_at = NOW() WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), (int) $adminUser['id']]);
        session_regenerate_id(true);
        admin_log('updated', 'admin_user', (int) $adminUser['id'], 'Staff user changed own password.');
        admin_flash('success', 'Password changed.');
        header('Location: ' . admin_url('dashboard.php'));
        exit;
    }
}
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><h2>Change Password</h2><?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?><form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><div class="admin-form-grid"><div class="admin-field"><label>Current Password</label><input class="admin-input" name="current_password" type="password" autocomplete="current-password" required></div><div class="admin-field"><label>New Password</label><input class="admin-input" name="new_password" type="password" autocomplete="new-password" required></div><div class="admin-field"><label>Confirm New Password</label><input class="admin-input" name="confirm_password" type="password" autocomplete="new-password" required></div></div><button class="admin-button" type="submit">Change Password</button></form></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>