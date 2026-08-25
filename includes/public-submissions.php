<?php

declare(strict_types=1);

require_once __DIR__ . '/content-database.php';

function ssvdp_form_old(string $key, array $source): string
{
    return e((string) ($source[$key] ?? ''));
}

function ssvdp_submission_value(array $source, string $key, int $maxLength): string
{
    $value = trim((string) ($source[$key] ?? ''));
    $value = str_replace("\0", '', $value);
    if (strlen($value) > $maxLength) {
        $value = substr($value, 0, $maxLength);
    }
    return $value;
}

function ssvdp_submission_security_error(string $formKey, array $source): ?string
{
    $token = (string) ($source['csrf_token'] ?? '');
    if (!verify_csrf_token($token)) {
        return 'We could not submit your message. Please check the information and try again.';
    }

    if (trim((string) ($source['website'] ?? '')) !== '') {
        return 'We could not submit your message. Please check the information and try again.';
    }

    $startedAt = (int) ($source['started_at'] ?? 0);
    if ($startedAt > 0 && time() - $startedAt < 2) {
        return 'We could not submit your message. Please check the information and try again.';
    }

    $lastKey = 'last_' . $formKey . '_submission';
    $now = time();
    $last = isset($_SESSION[$lastKey]) ? (int) $_SESSION[$lastKey] : 0;
    if ($last > 0 && ($now - $last) < 10) {
        return 'Please wait a moment before submitting again.';
    }

    return null;
}

function ssvdp_mark_submission_attempt(string $formKey): void
{
    $_SESSION['last_' . $formKey . '_submission'] = time();
}

function ssvdp_form_check_security(string $formKey, array $source): ?string
{
    $error = ssvdp_submission_security_error($formKey, $source);
    if (!$error) { ssvdp_mark_submission_attempt($formKey); }
    return $error;
}

function ssvdp_client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function ssvdp_user_agent(): string
{
    return substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
}

function ssvdp_handle_contact_submission(): array
{
    $values = array('name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => '');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['form_type'] ?? '') !== 'contact') {
        return array('success' => '', 'errors' => array(), 'values' => $values);
    }

    $values = array(
        'name' => ssvdp_submission_value($_POST, 'name', 120),
        'email' => ssvdp_submission_value($_POST, 'email', 254),
        'phone' => ssvdp_submission_value($_POST, 'phone', 40),
        'subject' => ssvdp_submission_value($_POST, 'subject', 160),
        'message' => ssvdp_submission_value($_POST, 'message', 5000),
    );

    $errors = array();
    $securityError = ssvdp_submission_security_error('contact', $_POST);
    if ($securityError) { $errors[] = $securityError; } else { ssvdp_mark_submission_attempt('contact'); }
    if ($values['name'] === '') { $errors[] = 'Enter your full name.'; }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address.'; }
    if ($values['subject'] === '') { $errors[] = 'Enter a subject.'; }
    if ($values['message'] === '') { $errors[] = 'Enter your message.'; }

    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'contact_messages')) { $errors[] = 'We could not submit your message. Please check the information and try again.'; }

    if ($errors) {
        return array('success' => '', 'errors' => $errors, 'values' => $values);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (full_name, email, phone, subject, message, status, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array($values['name'], $values['email'], $values['phone'] ?: null, $values['subject'], $values['message'], 'new', ssvdp_client_ip(), ssvdp_user_agent()));
    } catch (Throwable $exception) {
        error_log('Contact submission failed: ' . $exception->getMessage());
        return array('success' => '', 'errors' => array('We could not submit your message. Please check the information and try again.'), 'values' => $values);
    }

    return array('success' => 'Thank you. Your message has been received.', 'errors' => array(), 'values' => array('name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''));
}

function ssvdp_handle_get_involved_submission(): array
{
    $values = array('full_name' => '', 'email' => '', 'phone' => '', 'location' => '', 'involvement_type' => '', 'areas_of_interest' => array(), 'message' => '');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['form_type'] ?? '') !== 'get_involved') {
        return array('success' => '', 'errors' => array(), 'values' => $values);
    }

    $allowedAreas = array('Volunteering','Partnerships','Livelihoods','Education','Healthcare','Emergency Support');
    $areas = $_POST['areas_of_interest'] ?? array();
    if (!is_array($areas)) { $areas = array($areas); }
    $areas = array_values(array_intersect($allowedAreas, array_map(static fn($item) => trim((string) $item), $areas)));

    $allowedTypes = array('Volunteer','Partner','Donor','Community Member','Other');
    $involvementType = ssvdp_submission_value($_POST, 'involvement_type', 100);
    if (!in_array($involvementType, $allowedTypes, true)) { $involvementType = ''; }

    $values = array(
        'full_name' => ssvdp_submission_value($_POST, 'full_name', 120),
        'email' => ssvdp_submission_value($_POST, 'email', 254),
        'phone' => ssvdp_submission_value($_POST, 'phone', 40),
        'location' => ssvdp_submission_value($_POST, 'location', 160),
        'involvement_type' => $involvementType,
        'areas_of_interest' => $areas,
        'message' => ssvdp_submission_value($_POST, 'message', 5000),
    );

    $errors = array();
    $securityError = ssvdp_submission_security_error('get_involved', $_POST);
    if ($securityError) { $errors[] = $securityError; } else { ssvdp_mark_submission_attempt('get_involved'); }
    if ($values['full_name'] === '') { $errors[] = 'Enter your full name.'; }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address.'; }
    if ($values['involvement_type'] === '') { $errors[] = 'Choose how you want to get involved.'; }

    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'get_involved_submissions')) { $errors[] = 'We could not submit your message. Please check the information and try again.'; }

    if ($errors) {
        return array('success' => '', 'errors' => $errors, 'values' => $values);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO get_involved_submissions (full_name, email, phone, location, involvement_type, areas_of_interest, message, status, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array($values['full_name'], $values['email'], $values['phone'] ?: null, $values['location'] ?: null, $values['involvement_type'], implode(', ', $areas), $values['message'] ?: null, 'new', ssvdp_client_ip(), ssvdp_user_agent()));
    } catch (Throwable $exception) {
        error_log('Get involved submission failed: ' . $exception->getMessage());
        return array('success' => '', 'errors' => array('We could not submit your message. Please check the information and try again.'), 'values' => $values);
    }

    return array('success' => 'Thank you for your interest in SSVDP South Sudan. Our team will review your submission.', 'errors' => array(), 'values' => array('full_name' => '', 'email' => '', 'phone' => '', 'location' => '', 'involvement_type' => '', 'areas_of_interest' => array(), 'message' => ''));
}
function ssvdp_handle_newsletter_submission(): array
{
    $values = array('email' => '');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['form_type'] ?? '') !== 'newsletter') {
        return array('success' => '', 'errors' => array(), 'values' => $values);
    }

    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $values['email'] = $email;
    $errors = array();
    $securityError = ssvdp_form_check_security('newsletter', $_POST);
    if ($securityError) { $errors[] = $securityError; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address.'; }

    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'newsletter_subscribers')) { $errors[] = 'The newsletter service is temporarily unavailable.'; }

    if ($errors) {
        return array('success' => '', 'errors' => $errors, 'values' => $values);
    }

    $stmt = $pdo->prepare('SELECT id, status FROM newsletter_subscribers WHERE email = ? LIMIT 1');
    $stmt->execute(array($email));
    $existing = $stmt->fetch();

    if ($existing && $existing['status'] === 'active') {
        return array('success' => 'You are already subscribed.', 'errors' => array(), 'values' => array('email' => ''));
    }

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active', subscribed_at = CURRENT_TIMESTAMP, unsubscribed_at = NULL WHERE id = ?");
        $stmt->execute(array((int) $existing['id']));
    } else {
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, status, subscribed_at) VALUES (?, 'active', CURRENT_TIMESTAMP)");
        $stmt->execute(array($email));
    }

    return array('success' => 'Thank you for subscribing to SSVDP South Sudan updates.', 'errors' => array(), 'values' => array('email' => ''));
}