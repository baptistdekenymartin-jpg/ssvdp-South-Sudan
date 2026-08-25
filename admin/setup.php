<?php
require_once __DIR__ . '/../config/site-content.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}

function setup_admin_url(string $path = ''): string { return site_url('admin/' . ltrim($path, '/')); }
function setup_flash(string $type, string $message): void { $_SESSION['setup_flash'][] = compact('type', 'message'); }
function setup_messages(): array { $m = $_SESSION['setup_flash'] ?? array(); unset($_SESSION['setup_flash']); return $m; }

function setup_server_connection(): PDO
{
    global $databaseConfig;
    $dsn = sprintf('mysql:host=%s;charset=%s', $databaseConfig['host'], $databaseConfig['charset']);
    return new PDO($dsn, $databaseConfig['username'], $databaseConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
}

function setup_apply_migration(): PDO
{
    global $databaseConfig;
    $server = setup_server_connection();
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$databaseConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $server->exec("USE `{$databaseConfig['database']}`");
    $sql = file_get_contents(__DIR__ . '/../database/phase1_admin_dashboard.sql') . PHP_EOL . file_get_contents(__DIR__ . '/../database/phase2_admin_dashboard.sql') . PHP_EOL . file_get_contents(__DIR__ . '/../database/phase3_admin_dashboard.sql') . PHP_EOL . file_get_contents(__DIR__ . '/../database/phase4_admin_dashboard.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        $statement = preg_replace('/^\\xEF\\xBB\\xBF/', '', $statement);
        if (stripos($statement, 'CREATE DATABASE') === 0 || stripos($statement, 'USE ') === 0) {
            continue;
        }
        $server->exec($statement);
    }
    return get_database_connection();
}

function setup_has_admin(PDO $pdo): bool
{
    try { return (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0; }
    catch (Throwable $e) { return false; }
}

$error = '';
$hasAdmin = false;
try {
    $pdo = setup_apply_migration();
    $hasAdmin = setup_has_admin($pdo);
} catch (Throwable $exception) {
    $pdo = null;
    $error = 'Database setup could not run. Confirm MySQL is running and config/database.php credentials are correct.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo && !$hasAdmin) {
    $tokenOk = isset($_POST['csrf_token'], $_SESSION['setup_csrf']) && hash_equals($_SESSION['setup_csrf'], (string) $_POST['csrf_token']);
    if (!$tokenOk) {
        $error = 'Invalid setup token.';
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $username === '' || strlen($password) < 10) {
            $error = 'Enter a name, valid email, username and a password of at least 10 characters.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, username, password_hash, role, status) VALUES (?, ?, ?, ?, 'administrator', 'active')");
            $stmt->execute([$name, $email, $username, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: ' . setup_admin_url('login.php'));
            exit;
        }
    }
}

if (empty($_SESSION['setup_csrf'])) { $_SESSION['setup_csrf'] = bin2hex(random_bytes(32)); }
$messages = setup_messages();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Setup | SSVDP South Sudan</title><link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>"></head>
<body class="admin-login-body"><div class="admin-login-card admin-form">
<div class="admin-login-brand"><img src="<?php echo site_url('assets/images/logo/ssvdp-logo-cutout.png'); ?>" alt="SSVDP"><h1>Phase 1 Setup</h1><p>Staff Content Management</p></div>
<?php foreach ($messages as $m) : ?><div class="admin-alert admin-alert--<?php echo e($m['type']); ?>" style="margin:0"><?php echo e($m['message']); ?></div><?php endforeach; ?>
<?php if ($error) : ?><div class="admin-alert admin-alert--error" style="margin:0"><?php echo e($error); ?></div><?php endif; ?>
<?php if ($hasAdmin) : ?>
<p class="admin-muted">An administrator account already exists. Use the login page.</p><a class="admin-button" href="<?php echo setup_admin_url('login.php'); ?>">Go to Login</a>
<?php else : ?>
<form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['setup_csrf']); ?>">
<div class="admin-field"><label>Name</label><input class="admin-input" name="name" required></div>
<div class="admin-field"><label>Email</label><input class="admin-input" name="email" type="email" required></div>
<div class="admin-field"><label>Username</label><input class="admin-input" name="username" required></div>
<div class="admin-field"><label>Password</label><input class="admin-input" name="password" type="password" minlength="10" required><small>Use at least 10 characters. It will be stored with password_hash().</small></div>
<button class="admin-button" type="submit">Create Administrator</button></form>
<?php endif; ?>
</div></body></html>





