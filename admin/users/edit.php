<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
$adminTitle = 'Edit Staff User';
$activeNav = 'users';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, name, email, username, role, status FROM admin_users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { http_response_code(404); exit('User not found.'); }
function active_admins_edit(PDO $pdo): int { return (int) $pdo->query("SELECT COUNT(*) FROM admin_users WHERE role = 'administrator' AND status = 'active'")->fetchColumn(); }
$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $role = (string) ($_POST['role'] ?? 'editor');
    $status = (string) ($_POST['status'] ?? 'active');
    if ($name === '') { $errors[] = 'Full name is required.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email is required.'; }
    if ($username === '') { $errors[] = 'Username is required.'; }
    if (!in_array($role, array('administrator','editor'), true)) { $errors[] = 'Select a valid role.'; }
    if (!in_array($status, array('active','disabled'), true)) { $errors[] = 'Select a valid status.'; }
    if ((int) $id === (int) $adminUser['id'] && $status === 'disabled') { $errors[] = 'You cannot disable your own active account.'; }
    if ($user['role'] === 'administrator' && $user['status'] === 'active' && active_admins_edit($pdo) <= 1 && ($role !== 'administrator' || $status !== 'active')) { $errors[] = 'You cannot demote or disable the last active administrator.'; }
    if (!$errors) {
        try {
            $stmt = $pdo->prepare('UPDATE admin_users SET name = ?, email = ?, username = ?, role = ?, status = ? WHERE id = ?');
            $stmt->execute([$name, $email, $username, $role, $status, $id]);
            admin_log('updated', 'admin_user', $id, 'Staff user updated.');
            admin_flash('success', 'Staff user updated.');
            header('Location: ' . admin_url('users/'));
            exit;
        } catch (Throwable $e) { $errors[] = 'Email or username is already in use.'; }
    }
    $user = array('id'=>$id,'name'=>$name,'email'=>$email,'username'=>$username,'role'=>$role,'status'=>$status);
}
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel"><h2>Edit Staff User</h2><?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?><form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><div class="admin-form-grid"><div class="admin-field"><label>Full Name</label><input class="admin-input" name="name" value="<?php echo e($user['name']); ?>" required></div><div class="admin-field"><label>Email</label><input class="admin-input" name="email" type="email" value="<?php echo e($user['email']); ?>" required></div><div class="admin-field"><label>Username</label><input class="admin-input" name="username" value="<?php echo e($user['username']); ?>" required></div><div class="admin-field"><label>Role</label><select class="admin-select" name="role"><option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option><option value="administrator" <?php echo $user['role'] === 'administrator' ? 'selected' : ''; ?>>Administrator</option></select></div><div class="admin-field"><label>Status</label><select class="admin-select" name="status"><option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option><option value="disabled" <?php echo $user['status'] === 'disabled' ? 'selected' : ''; ?>>Disabled</option></select></div></div><div class="admin-actions"><button class="admin-button" type="submit">Save User</button><a class="admin-button admin-button--light" href="<?php echo admin_url('users/'); ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>