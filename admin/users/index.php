<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
admin_require_csrf();
$adminTitle = 'Staff Users';
$activeNav = 'users';

function active_admin_count(PDO $pdo): int { return (int) $pdo->query("SELECT COUNT(*) FROM admin_users WHERE role = 'administrator' AND status = 'active'")->fetchColumn(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    if ($id > 0 && $id !== (int) $adminUser['id']) {
        $stmt = $pdo->prepare('SELECT id, role, status FROM admin_users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $target = $stmt->fetch();
        if ($target) {
            if ($action === 'disable') {
                if ($target['role'] === 'administrator' && $target['status'] === 'active' && active_admin_count($pdo) <= 1) {
                    admin_flash('error', 'You cannot disable the last active administrator.');
                } else {
                    $pdo->prepare("UPDATE admin_users SET status = 'disabled' WHERE id = ?")->execute([$id]);
                    admin_log('updated', 'admin_user', $id, 'Staff user disabled.');
                    admin_flash('success', 'User disabled.');
                }
            } elseif ($action === 'enable') {
                $pdo->prepare("UPDATE admin_users SET status = 'active' WHERE id = ?")->execute([$id]);
                admin_log('updated', 'admin_user', $id, 'Staff user enabled.');
                admin_flash('success', 'User enabled.');
            }
        }
    } else {
        admin_flash('error', 'You cannot disable your own active account.');
    }
    header('Location: ' . admin_url('users/'));
    exit;
}

$users = $pdo->query('SELECT id, name, email, username, role, status, last_login, created_at FROM admin_users ORDER BY created_at DESC, id DESC')->fetchAll();
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-table-card">
    <div class="admin-toolbar"><div><h2>Staff Users</h2><p class="admin-muted">Manage dashboard staff access.</p></div><a class="admin-button" href="<?php echo admin_url('users/add.php'); ?>">Add Staff User</a></div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Username</th><th>Role</th><th>Status</th><th>Last Login</th><th>Created</th><th>Actions</th></tr></thead><tbody>
    <?php foreach ($users as $user) : ?><tr><td><strong><?php echo e($user['name']); ?></strong></td><td><?php echo e($user['email']); ?></td><td><?php echo e($user['username']); ?></td><td><?php echo e(ucfirst($user['role'])); ?></td><td><?php echo '<span class="admin-status admin-status--' . e($user['status']) . '">' . e(ucfirst($user['status'])) . '</span>'; ?></td><td><?php echo e($user['last_login'] ? ssvdp_format_date($user['last_login'], '') : 'Never'); ?></td><td><?php echo e(ssvdp_format_date($user['created_at'], '')); ?></td><td><div class="admin-row-actions"><a href="<?php echo admin_url('users/edit.php?id=' . (int) $user['id']); ?>">Edit</a><a href="<?php echo admin_url('users/reset-password.php?id=' . (int) $user['id']); ?>">Reset Password</a><?php if ((int) $user['id'] !== (int) $adminUser['id']) : ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>"><button name="action" value="<?php echo $user['status'] === 'active' ? 'disable' : 'enable'; ?>" type="submit"><?php echo $user['status'] === 'active' ? 'Disable' : 'Enable'; ?></button></form><?php endif; ?></div></td></tr><?php endforeach; ?>
    </tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>