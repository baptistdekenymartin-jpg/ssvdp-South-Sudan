<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
$adminTitle = 'Reset Staff Password';
$activeNav = 'users';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, name, email, username, role FROM admin_users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { http_response_code(404); exit('User not found.'); }
$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    if ($password !== $confirm) { $errors[] = 'Passwords do not match.'; }
    $errors = array_merge($errors, admin_password_errors($password));
    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ?, password_changed_at = NOW() WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
        admin_log('updated', 'admin_user', $id, 'Staff password reset by administrator.');
        admin_flash('success', 'Password reset saved.');
        header('Location: ' . admin_url('users/'));
        exit;
    }
}
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><h2>Reset Password</h2><p class="admin-muted">Reset password for <?php echo e($user['name']); ?>. The existing password is never displayed.</p><?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?><form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><div class="admin-form-grid"><div class="admin-field"><label>New Temporary Password</label><input class="admin-input" name="password" type="password" autocomplete="new-password" required></div><div class="admin-field"><label>Confirm New Password</label><input class="admin-input" name="confirm_password" type="password" autocomplete="new-password" required></div></div><div class="admin-actions"><button class="admin-button" type="submit">Reset Password</button><a class="admin-button admin-button--light" href="<?php echo admin_url('users/'); ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>