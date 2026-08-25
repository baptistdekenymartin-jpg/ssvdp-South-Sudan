<?php

ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: frame-ancestors 'self'");
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

require_once __DIR__ . '/../../config/site-content.php';
require_once __DIR__ . '/../../includes/content-database.php';

const ADMIN_IDLE_TIMEOUT = 2700;
const ADMIN_ABSOLUTE_TIMEOUT = 28800;
const ADMIN_IMAGE_MAX_BYTES = 8388608;
const ADMIN_DOCUMENT_MAX_BYTES = 20971520;

function admin_url(string $path = ''): string
{
    return site_url('admin/' . ltrim($path, '/'));
}

function admin_upload_url(string $path): string
{
    return site_url($path);
}

function admin_client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function admin_db(): ?PDO
{
    return ssvdp_db();
}

function admin_require_db(): PDO
{
    $pdo = admin_db();
    if (!$pdo) {
        admin_flash('error', 'Database connection is not available. Run setup after confirming config/database.php.');
        header('Location: ' . admin_url('setup.php'));
        exit;
    }
    return $pdo;
}

function admin_has_users(): bool
{
    $pdo = admin_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'admin_users')) {
        return false;
    }
    try {
        return (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0;
    } catch (Throwable $exception) {
        return false;
    }
}

function admin_clear_session(string $reason = ''): void
{
    $_SESSION = array();
    if ($reason !== '') {
        $_SESSION['admin_flash'][] = array('type' => 'error', 'message' => $reason);
    }
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function admin_current_user(): ?array
{
    if (empty($_SESSION['admin_user_id'])) {
        return null;
    }

    $now = time();
    $started = (int) ($_SESSION['admin_started_at'] ?? $now);
    $lastSeen = (int) ($_SESSION['admin_last_seen_at'] ?? $now);
    if (($now - $lastSeen) > ADMIN_IDLE_TIMEOUT || ($now - $started) > ADMIN_ABSOLUTE_TIMEOUT) {
        admin_clear_session('Your session expired. Please sign in again.');
        return null;
    }
    $_SESSION['admin_last_seen_at'] = $now;

    $pdo = admin_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'admin_users')) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT id, name, email, username, role, status, last_login FROM admin_users WHERE id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int) $_SESSION['admin_user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        admin_clear_session('Your account is not active. Please contact an administrator.');
        return null;
    }
    return $user;
}

function admin_require_auth(): array
{
    $user = admin_current_user();
    if (!$user) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
    return $user;
}

function admin_is_administrator(?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    return $user && ($user['role'] ?? '') === 'administrator';
}

function admin_forbidden(): void
{
    http_response_code(403);
    exit('403 Forbidden');
}

function admin_require_role(string $role): array
{
    $user = admin_require_auth();
    if ($role === 'administrator' && !admin_is_administrator($user)) {
        admin_forbidden();
    }
    return $user;
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf_token'];
}

function admin_verify_csrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['admin_csrf_token'])
        && hash_equals($_SESSION['admin_csrf_token'], $token);
}

function admin_require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !admin_verify_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        exit('Invalid request token.');
    }
}

function admin_flash(string $type, string $message): void
{
    $_SESSION['admin_flash'][] = array('type' => $type, 'message' => $message);
}

function admin_take_flash(): array
{
    $messages = $_SESSION['admin_flash'] ?? array();
    unset($_SESSION['admin_flash']);
    return $messages;
}

function admin_log(string $action, string $entityType, ?int $entityId, string $description): void
{
    $pdo = admin_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'admin_activity_log')) {
        return;
    }
    $userId = isset($_SESSION['admin_user_id']) ? (int) $_SESSION['admin_user_id'] : null;
    try {
        $stmt = $pdo->prepare('INSERT INTO admin_activity_log (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $action, $entityType, $entityId, $description, admin_client_ip()]);
    } catch (Throwable $exception) {
        $stmt = $pdo->prepare('INSERT INTO admin_activity_log (user_id, action, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $action, $entityType, $entityId, $description]);
    }
}

function admin_login_is_blocked(PDO $pdo, string $identifier, string $ip): bool
{
    if (!ssvdp_table_exists($pdo, 'admin_login_attempts')) { return false; }
    $pdo->exec('DELETE FROM admin_login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE success = 0 AND identifier = ? AND ip_address = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute([strtolower($identifier), $ip]);
    return (int) $stmt->fetchColumn() >= 5;
}

function admin_record_login_attempt(PDO $pdo, string $identifier, string $ip, bool $success): void
{
    if (!ssvdp_table_exists($pdo, 'admin_login_attempts')) { return; }
    $stmt = $pdo->prepare('INSERT INTO admin_login_attempts (identifier, ip_address, success) VALUES (?, ?, ?)');
    $stmt->execute([strtolower($identifier), $ip, $success ? 1 : 0]);
}

function admin_password_errors(string $password): array
{
    $errors = array();
    if (strlen($password) < 12) { $errors[] = 'Password must be at least 12 characters.'; }
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password should include upper and lower case letters, a number and a symbol.';
    }
    return $errors;
}

function admin_slug_unique(PDO $pdo, string $table, string $slug, ?int $ignoreId = null): string
{
    $base = ssvdp_slugify($slug);
    $candidate = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = ?" . ($ignoreId ? ' AND id <> ?' : '') . ' LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ignoreId ? [$candidate, $ignoreId] : [$candidate]);
        if (!$stmt->fetchColumn()) { return $candidate; }
        $candidate = $base . '-' . $i;
        $i++;
    }
}

function admin_reject_executable_name(string $name): void
{
    $lower = strtolower($name);
    $blocked = array('.php', '.php3', '.php4', '.php5', '.phtml', '.phar', '.exe', '.com', '.bat', '.cmd', '.ps1', '.vbs', '.js', '.sh', '.htaccess', '.scr');
    foreach ($blocked as $ext) {
        if (str_ends_with($lower, $ext) || str_contains($lower, $ext . '.')) {
            throw new RuntimeException('Executable uploads are not allowed.');
        }
    }
}

function admin_validate_upload(array $file): void
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed.');
    }
    if (($file['size'] ?? 0) > ADMIN_IMAGE_MAX_BYTES) {
        throw new RuntimeException('Image uploads must be 8 MB or smaller.');
    }
    admin_reject_executable_name((string) ($file['name'] ?? ''));
    $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG and WEBP images are allowed.');
    }
    if (!@getimagesize($file['tmp_name'])) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }
}

function admin_store_upload(array $file, string $folder): string
{
    admin_validate_upload($file);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $ext = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp')[$mime];
    $relativeDir = 'uploads/' . trim($folder, '/');
    $absoluteDir = dirname(__DIR__, 2) . '/' . $relativeDir;
    if (!is_dir($absoluteDir)) { mkdir($absoluteDir, 0775, true); }
    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = $absoluteDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }
    return $relativeDir . '/' . $filename;
}

function admin_get_categories(): array
{
    return array('Health, Nutrition and Women\'s Empowerment', 'Livelihoods and Practical Skills', 'Community Empowerment', 'Emergency Support', 'Training', 'Partnerships', 'Events');
}

function admin_store_document_upload(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Document upload failed.');
    }
    if (($file['size'] ?? 0) > ADMIN_DOCUMENT_MAX_BYTES) {
        throw new RuntimeException('Document uploads must be 20 MB or smaller.');
    }
    admin_reject_executable_name((string) ($file['name'] ?? ''));
    $allowed = array(
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    );
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only PDF, DOC and DOCX documents are allowed.');
    }
    $relativeDir = 'uploads/documents';
    $absoluteDir = dirname(__DIR__, 2) . '/' . $relativeDir;
    if (!is_dir($absoluteDir)) { mkdir($absoluteDir, 0775, true); }
    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $target = $absoluteDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded document.');
    }
    return $relativeDir . '/' . $filename;
}

function admin_get_programme_names(): array
{
    return array('Vocational Training', 'Education', 'Healthcare Services', 'Child Care and Protection', 'Food Security and Nutrition', 'Agriculture and Livelihoods', 'Humanitarian Assistance', 'Social Enterprise and Community Development', 'Income Generating Projects', 'Baby Feeding', 'Kitchen Gardening', 'Emergency / IDP Support');
}