<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
$adminTitle = 'Add Staff User';
$activeNav = 'users';
$errors = array();
$values = array('name'=>'','email'=>'','username'=>'','role'=>'editor','status'=>'active');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    foreach ($values as $key => $value) { $values[$key] = trim((string) ($_POST[$key] ?? $value)); }
    $password = (string) ($_POST['password'] ?? '');
    if ($values['name'] === '') { $errors[] = 'Full name is required.'; }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email is required.'; }
    if ($values['username'] === '') { $errors[] = 'Username is required.'; }
    if (!in_array($values['role'], array('administrator','editor'), true)) { $errors[] = 'Select a valid role.'; }
    if (!in_array($values['status'], array('active','disabled'), true)) { $errors[] = 'Select a valid status.'; }
    $errors = array_merge($errors, admin_password_errors($password));
    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO admin_users (name, email, username, password_hash, role, status, password_changed_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$values['name'], $values['email'], $values['username'], password_hash($password, PASSWORD_DEFAULT), $values['role'], $values['status']]);
            $id = (int) $pdo->lastInsertId();
            admin_log('created', 'admin_user', $id, 'Staff user created.');
            admin_flash('success', 'Staff user created.');
            header('Location: ' . admin_url('users/'));
            exit;
        } catch (Throwable $e) { $errors[] = 'Email or username is already in use.'; }
    }
}
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><h2>Add Staff User</h2><?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?><form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><div class="admin-form-grid"><div class="admin-field"><label>Full Name</label><input class="admin-input" name="name" value="<?php echo e($values['name']); ?>" required></div><div class="admin-field"><label>Email</label><input class="admin-input" name="email" type="email" value="<?php echo e($values['email']); ?>" required></div><div class="admin-field"><label>Username</label><input class="admin-input" name="username" value="<?php echo e($values['username']); ?>" required></div><div class="admin-field"><label>Temporary Password</label><input class="admin-input" name="password" type="password" autocomplete="new-password" required><small>Minimum 12 characters with upper/lower case, number and symbol.</small></div><div class="admin-field"><label>Role</label><select class="admin-select" name="role"><option value="editor" <?php echo $values['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option><option value="administrator" <?php echo $values['role'] === 'administrator' ? 'selected' : ''; ?>>Administrator</option></select></div><div class="admin-field"><label>Status</label><select class="admin-select" name="status"><option value="active" <?php echo $values['status'] === 'active' ? 'selected' : ''; ?>>Active</option><option value="disabled" <?php echo $values['status'] === 'disabled' ? 'selected' : ''; ?>>Disabled</option></select></div></div><div class="admin-actions"><button class="admin-button" type="submit">Create User</button><a class="admin-button admin-button--light" href="<?php echo admin_url('users/'); ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>