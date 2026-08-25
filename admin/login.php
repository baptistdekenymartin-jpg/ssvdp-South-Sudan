<?php
require_once __DIR__ . '/includes/auth.php';

if (admin_current_user()) {
    header('Location: ' . admin_url('dashboard.php'));
    exit;
}

if (!admin_has_users()) {
    header('Location: ' . admin_url('setup.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $pdo = admin_db();
    $ip = admin_client_ip();
    $genericError = 'Unable to sign in. Please check your credentials or try again later.';

    if ($pdo && $login !== '' && !admin_login_is_blocked($pdo, $login, $ip)) {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE (email = ? OR username = ?) AND status = 'active' LIMIT 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            admin_record_login_attempt($pdo, $login, $ip, true);
            session_regenerate_id(true);
            $_SESSION['admin_user_id'] = (int) $user['id'];
            $_SESSION['admin_started_at'] = time();
            $_SESSION['admin_last_seen_at'] = time();
            $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([(int) $user['id']]);
            admin_log('login', 'admin_user', (int) $user['id'], 'Staff user signed in.');
            header('Location: ' . admin_url('dashboard.php'));
            exit;
        }
        admin_record_login_attempt($pdo, $login, $ip, false);
    } elseif ($pdo && $login !== '') {
        admin_record_login_attempt($pdo, $login, $ip, false);
    }
    $error = $genericError;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SSVDP South Sudan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>">
</head>
<body class="admin-login-body">
    <form class="admin-login-card admin-form" method="post" action="<?php echo admin_url('login.php'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
        <div class="admin-login-brand">
            <img src="<?php echo site_url('assets/images/logo/ssvdp-logo-cutout.png'); ?>" alt="SSVDP South Sudan">
            <h1>SSVDP South Sudan</h1>
            <p>Staff Content Management</p>
        </div>
        <?php foreach (admin_take_flash() as $flashMessage) : ?><div class="admin-alert admin-alert--<?php echo e($flashMessage['type']); ?>" style="margin:0"><?php echo e($flashMessage['message']); ?></div><?php endforeach; ?>
        <?php if ($error) : ?><div class="admin-alert admin-alert--error" style="margin:0"><?php echo e($error); ?></div><?php endif; ?>
        <div class="admin-field">
            <label for="login">Username / Email</label>
            <input class="admin-input" id="login" name="login" type="text" autocomplete="username" required>
        </div>
        <div class="admin-field">
            <label for="password">Password</label>
            <div class="admin-password-row">
                <input class="admin-input" id="password" name="password" type="password" autocomplete="current-password" required>
                <button type="button" data-password-toggle>Show</button>
            </div>
        </div>
        <button class="admin-button" type="submit">Sign In</button>
    </form>
<script>
document.querySelector('[data-password-toggle]')?.addEventListener('click', function () {
    const input = document.getElementById('password');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    this.textContent = show ? 'Hide' : 'Show';
});
</script>
</body>
</html>