<?php

require_once __DIR__ . '/../../includes/security.php';
ssvdp_configure_error_handling();
ssvdp_require_https_in_production();
ssvdp_security_headers(true);

ssvdp_start_secure_session('SSVDPADMIN');


require_once __DIR__ . '/../../config/site-content.php';
require_once __DIR__ . '/../../includes/content-database.php';

const ADMIN_IDLE_TIMEOUT = 2700;
const ADMIN_ABSOLUTE_TIMEOUT = 28800;
const ADMIN_IMAGE_MAX_BYTES = 8388608;
const ADMIN_DOCUMENT_MAX_BYTES = 20971520;


function admin_allowed_roles(): array
{
    return array('administrator', 'editor', 'reviewer');
}

function admin_role_label(string $role): string
{
    return array(
        'administrator' => 'Super Administrator',
        'editor' => 'Content Editor',
        'reviewer' => 'Reviewer / Publisher'
    )[$role] ?? 'Content Editor';
}

function admin_ensure_security_schema(PDO $pdo): void
{
    try {
        $column = $pdo->query("SHOW COLUMNS FROM admin_users LIKE 'role'")->fetch();
        $type = strtolower((string) ($column['Type'] ?? ''));
        if ($type !== '' && !str_contains($type, 'reviewer')) {
            $pdo->exec("ALTER TABLE admin_users MODIFY role ENUM('administrator','editor','reviewer') NOT NULL DEFAULT 'editor'");
        }
    } catch (Throwable $exception) {}

    try {
        $column = $pdo->query("SHOW COLUMNS FROM admin_users LIKE 'status'")->fetch();
        $type = strtolower((string) ($column['Type'] ?? ''));
        if ($type !== '' && !str_contains($type, 'disabled')) {
            $pdo->exec("UPDATE admin_users SET status = 'disabled' WHERE status = 'inactive'");
            $pdo->exec("ALTER TABLE admin_users MODIFY status ENUM('active','disabled') NOT NULL DEFAULT 'active'");
        }
    } catch (Throwable $exception) {}

    try {
        $columns = array(
            'password_changed_at' => "ALTER TABLE admin_users ADD password_changed_at DATETIME NULL",
            'two_factor_enabled' => "ALTER TABLE admin_users ADD two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0",
            'two_factor_secret' => "ALTER TABLE admin_users ADD two_factor_secret VARCHAR(255) NULL"
        );
        foreach ($columns as $columnName => $sql) {
            $exists = $pdo->query("SHOW COLUMNS FROM admin_users LIKE '" . $columnName . "'")->fetch();
            if (!$exists) { $pdo->exec($sql); }
        }
    } catch (Throwable $exception) {}
}

function admin_can(string $permission, ?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    $role = (string) ($user['role'] ?? '');
    if ($role === 'administrator') { return true; }

    $matrix = array(
        'editor' => array(
            'content.manage', 'content.create', 'content.edit', 'content.submit_review',
            'communications.view', 'communications.basic', 'newsletter.view'
        ),
        'reviewer' => array(
            'content.manage', 'content.edit', 'content.publish', 'content.return_draft', 'content.unpublish', 'content.archive',
            'communications.view', 'communications.basic', 'communications.resolve', 'communications.archive',
            'newsletter.view', 'newsletter.manage'
        ),
    );

    return in_array($permission, $matrix[$role] ?? array(), true);
}

function admin_role_is_editor(?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    return (string) ($user['role'] ?? '') === 'editor';
}

function admin_role_is_reviewer(?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    return (string) ($user['role'] ?? '') === 'reviewer';
}

function admin_content_user_owns(?array $row, ?array $user = null): bool
{
    if (!$row) { return true; }
    $user = $user ?: admin_current_user();
    $creator = $row['created_by'] ?? null;
    return $creator === null || $creator === '' || (int) $creator === (int) ($user['id'] ?? 0);
}

function admin_can_edit_content(?array $row = null, ?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    if (admin_is_administrator($user)) { return true; }
    $status = (string) ($row['status'] ?? 'draft');
    if (admin_role_is_editor($user)) {
        return in_array($status, array('draft', 'pending_review'), true) && admin_content_user_owns($row, $user);
    }
    if (admin_role_is_reviewer($user)) {
        return in_array($status, array('pending_review', 'published'), true);
    }
    return false;
}

function admin_can_transition_content(string $action, ?array $row = null, ?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    if (admin_is_administrator($user)) { return true; }
    $status = (string) ($row['status'] ?? 'draft');
    if (admin_role_is_editor($user)) {
        return in_array($action, array('draft', 'submit_review'), true)
            && in_array($status, array('draft', 'pending_review'), true)
            && admin_content_user_owns($row, $user);
    }
    if (admin_role_is_reviewer($user)) {
        if ($action === 'publish') { return $status === 'pending_review'; }
        if ($action === 'return_draft') { return $status === 'pending_review'; }
        if ($action === 'unpublish') { return $status === 'published'; }
        if ($action === 'archive') { return in_array($status, array('pending_review', 'published'), true); }
    }
    return false;
}

function admin_content_status_for_action(string $action, ?array $row = null): string
{
    return array(
        'draft' => 'draft',
        'submit_review' => 'pending_review',
        'pending_review' => 'pending_review',
        'publish' => 'published',
        'return_draft' => 'draft',
        'unpublish' => 'draft',
        'archive' => 'archived',
    )[$action] ?? (string) ($row['status'] ?? 'draft');
}

function admin_content_action_label(string $action): string
{
    return array(
        'draft' => 'Save Draft',
        'submit_review' => 'Submit for Review',
        'publish' => 'Publish',
        'return_draft' => 'Return to Draft',
        'unpublish' => 'Unpublish',
        'archive' => 'Archive',
    )[$action] ?? ucfirst(str_replace('_', ' ', $action));
}

function admin_content_allowed_actions(?array $row = null, ?array $user = null): array
{
    $user = $user ?: admin_current_user();
    $status = (string) ($row['status'] ?? 'draft');
    $actions = admin_is_administrator($user)
        ? array('draft', 'submit_review', 'publish', 'return_draft', 'unpublish', 'archive')
        : (admin_role_is_editor($user) ? array('draft', 'submit_review') : array('publish', 'return_draft', 'unpublish', 'archive'));
    return array_values(array_filter($actions, static fn(string $action): bool => admin_can_transition_content($action, $row, $user)));
}

function admin_can_communication_action(string $action, ?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    if (admin_is_administrator($user)) { return true; }
    if (in_array($action, array('read', 'unread', 'contacted', 'save_notes'), true)) {
        return admin_can('communications.basic', $user);
    }
    if (in_array($action, array('resolved', 'in_progress', 'closed'), true)) {
        return admin_can('communications.resolve', $user);
    }
    if ($action === 'archive') { return admin_can('communications.archive', $user); }
    if ($action === 'delete') { return false; }
    return false;
}

function admin_can_newsletter_action(string $action, ?array $user = null): bool
{
    $user = $user ?: admin_current_user();
    if (admin_is_administrator($user)) { return true; }
    if (in_array($action, array('unsubscribe', 'reactivate'), true)) { return admin_can('newsletter.manage', $user); }
    if ($action === 'export' || $action === 'delete') { return false; }
    return admin_can('newsletter.view', $user);
}

function admin_require_permission(string $permission): array
{
    $user = admin_require_auth();
    if (!admin_can($permission, $user)) {
        admin_forbidden();
    }
    return $user;
}
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
    admin_ensure_security_schema($pdo);
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

    $stmt = $pdo->prepare("SELECT id, name, email, username, role, status, last_login, password_changed_at FROM admin_users WHERE id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int) $_SESSION['admin_user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        admin_clear_session('Your account is not active. Please contact an administrator.');
        return null;
    }
    $sessionPasswordChangedAt = (string) ($_SESSION['admin_password_changed_at'] ?? '');
    $currentPasswordChangedAt = (string) ($user['password_changed_at'] ?? '');
    if ($currentPasswordChangedAt !== '' && $sessionPasswordChangedAt !== '' && $currentPasswordChangedAt !== $sessionPasswordChangedAt) {
        admin_clear_session('Your password changed. Please sign in again.');
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
    } elseif ($role !== 'administrator' && ($user['role'] ?? '') !== $role) {
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

function admin_login_throttle_seconds(PDO $pdo, string $identifier, string $ip): int
{
    if (!ssvdp_table_exists($pdo, 'admin_login_attempts')) { return 0; }
    $pdo->exec('DELETE FROM admin_login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE success = 0 AND (identifier = ? OR ip_address = ?) AND attempted_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute([strtolower($identifier), $ip]);
    $failures = (int) $stmt->fetchColumn();
    if ($failures >= 10) { return 3600; }
    if ($failures >= 5) { return 900; }
    if ($failures >= 3) { return 300; }
    return 0;
}

function admin_login_is_blocked(PDO $pdo, string $identifier, string $ip): bool
{
    return admin_login_throttle_seconds($pdo, $identifier, $ip) > 0;
}

function admin_record_login_attempt(PDO $pdo, string $identifier, string $ip, bool $success): void
{
    if (!ssvdp_table_exists($pdo, 'admin_login_attempts')) { return; }
    if ($success) {
        $delete = $pdo->prepare('DELETE FROM admin_login_attempts WHERE identifier = ? OR ip_address = ?');
        $delete->execute([strtolower($identifier), $ip]);
        return;
    }
    $stmt = $pdo->prepare('INSERT INTO admin_login_attempts (identifier, ip_address, success) VALUES (?, ?, ?)');
    $stmt->execute([strtolower($identifier), $ip, 0]);
}

function admin_password_errors(string $password): array
{
    $errors = array();
    $lower = strtolower($password);
    if (strlen($password) < 12) { $errors[] = 'Password must be at least 12 characters.'; }
    if (in_array($lower, array('password123!', 'admin123456!', 'ssvp123456!', 'qwerty12345!', 'letmein12345!'), true)) { $errors[] = 'Choose a less common password.'; }
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password should include upper and lower case letters, a number and a symbol.';
    }
    return $errors;
}

function admin_slug_unique(PDO $pdo, string $table, string $slug, ?int $ignoreId = null): string
{
    if (preg_match('/^[a-z0-9_]+$/', $table) !== 1) {
        throw new InvalidArgumentException('Invalid slug table name.');
    }
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

function admin_slug_is_valid(?string $slug): bool
{
    $slug = trim((string) $slug);
    return $slug !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1;
}

function admin_slug_for_save(PDO $pdo, string $table, string $source, ?array $existing = null): string
{
    $existingSlug = trim((string) ($existing['slug'] ?? ''));
    $ignoreId = isset($existing['id']) ? (int) $existing['id'] : null;
    if ($existingSlug !== '' && admin_slug_is_valid($existingSlug)) {
        return admin_slug_unique($pdo, $table, $existingSlug, $ignoreId);
    }
    return admin_slug_unique($pdo, $table, $source, $ignoreId);
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
    return admin_news_categories();
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
    return array('Vocational Training', 'Education', 'Healthcare Services', 'Child Protection, Rehabilitation and Re-integration', 'Nutrition', 'Agriculture and Livelihoods', 'Humanitarian Assistance', 'Self-Reliance Initiative', 'Income Generating Projects', 'Baby Feeding', 'Kitchen Gardening', 'Emergency / IDP Support');
}
function admin_content_locations(): array
{
    return array('Juba', 'Lologo', 'Nyarjwa', 'Rejaf', 'Kworijik', 'Luri');
}

function admin_content_programmes(): array
{
    return array('Vocational Training', 'Income Generating Projects', 'Agriculture / Farming', 'Poultry Farming', 'Jam Production', 'Youth Skills Development', 'Community Development', 'Emergency Support');
}

function admin_content_statuses(): array
{
    return array('draft' => 'Draft', 'pending_review' => 'Pending Review', 'published' => 'Published', 'archived' => 'Archived');
}

function admin_news_categories(): array
{
    return array('Programme Update', 'Community Development', 'Youth Empowerment', 'Emergency Support', 'Training', 'Announcement', 'Partnership', 'Success Story');
}

function admin_featured_activity_categories(): array
{
    return array('Programme Update', 'Community Development', 'Youth Empowerment', 'Training', 'Emergency Support', 'Community Activity');
}

function admin_event_types(): array
{
    return array('Event', 'Announcement', 'Training', 'Meeting', 'Workshop', 'Community Activity', 'Awareness Campaign');
}

function admin_partner_types(): array
{
    return array('Partner', 'Donor', 'Government Institution', 'Church / Faith-Based Organization', 'NGO', 'UN / International Organization', 'Community Organization', 'Private Sector');
}

function admin_document_types(): array
{
    return array('Annual Report', 'Programme Report', 'Policy', 'Guideline', 'Publication', 'Newsletter', 'Brochure', 'Form', 'Training Material');
}

function admin_impact_types(): array
{
    return array('Beneficiaries Reached', 'Training Completed', 'Livelihood Support', 'Emergency Assistance', 'Community Development', 'Youth Empowerment', 'Agriculture');
}

function admin_gallery_album_titles(): array
{
    return array(
        'Vocational Training' => 'Vocational Training Activities',
        'Income Generating Projects' => 'Income Generating Activities',
        'Agriculture / Farming' => 'Agriculture and Farming Activities',
        'Poultry Farming' => 'Poultry Farming Activities',
        'Jam Production' => 'Jam Production Activities',
        'Youth Skills Development' => 'Youth Skills Development Activities',
        'Community Development' => 'Community Development Activities',
        'Emergency Support' => 'Emergency Support Activities'
    );
}

function admin_select_other_state(?string $value, array $options): array
{
    $value = trim((string) $value);
    if ($value !== '' && in_array($value, $options, true)) {
        return array($value, '');
    }
    return array($value === '' ? '' : 'Others', $value);
}

function admin_select_other_value(string $selected, string $custom, array $options): string
{
    $selected = trim($selected);
    $custom = trim($custom);
    if ($selected === 'Others') {
        return $custom;
    }
    return in_array($selected, $options, true) ? $selected : '';
}

function admin_render_select_other(string $name, string $label, ?string $value, array $options, string $customLabel, bool $required = false, array $fieldErrors = array()): void
{
    [$selected, $custom] = admin_select_other_state($value, $options);
    $customName = $name . '_custom';
    echo '<div class="admin-field"><label>' . e($label) . '</label><select class="admin-select" name="' . e($name) . '_select" data-other-select="' . e($customName) . '"' . ($required ? ' required' : '') . '><option value="">Select ' . e(strtolower($label)) . '</option>';
    foreach ($options as $option) {
        echo '<option value="' . e($option) . '" ' . ($selected === $option ? 'selected' : '') . '>' . e($option) . '</option>';
    }
    echo '<option value="Others" ' . ($selected === 'Others' ? 'selected' : '') . '>Others</option></select>';
    echo '<div class="admin-field" data-other-field="' . e($customName) . '" ' . ($selected === 'Others' ? '' : 'hidden') . '><label>' . e($customLabel) . '</label><input class="admin-input" name="' . e($customName) . '" value="' . e($custom) . '" ' . ($selected === 'Others' ? 'required' : '') . '></div>';
    if (!empty($fieldErrors[$name])) { echo '<small class="admin-field-error">' . e($fieldErrors[$name]) . '</small>'; }
    echo '</div>';
}

function admin_resolve_select_other_post(string $name, array $options): string
{
    return admin_select_other_value((string) ($_POST[$name . '_select'] ?? ''), (string) ($_POST[$name . '_custom'] ?? ''), $options);
}

function admin_select_other_required_error(string $name, string $label, array $options): string
{
    $selected = trim((string) ($_POST[$name . '_select'] ?? ''));
    $custom = trim((string) ($_POST[$name . '_custom'] ?? ''));
    if ($selected === '') { return $label . ' is required.'; }
    if ($selected === 'Others' && $custom === '') { return 'Custom ' . strtolower($label) . ' is required.'; }
    if ($selected !== 'Others' && !in_array($selected, $options, true)) { return $label . ' is invalid.'; }
    return '';
}
