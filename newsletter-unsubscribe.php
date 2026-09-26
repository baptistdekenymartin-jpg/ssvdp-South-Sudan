<?php
require_once __DIR__ . '/includes/newsletter.php';
$pageRobots = 'noindex, nofollow';

$token = trim((string) ($_GET['token'] ?? ''));
$success = '';
$error = '';

if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $error = 'This unsubscribe link is invalid.';
} else {
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'newsletter_subscribers')) {
        $error = 'The newsletter service is temporarily unavailable.';
    } else {
        ssvdp_newsletter_ensure_schema($pdo);
        try {
            $stmt = $pdo->prepare('SELECT id, status FROM newsletter_subscribers WHERE unsubscribe_token = ? LIMIT 1');
            $stmt->execute(array($token));
            $subscriber = $stmt->fetch();
            if (!$subscriber) {
                $error = 'This unsubscribe link is invalid or has expired.';
            } elseif ($subscriber['status'] === 'unsubscribed') {
                $success = 'This email is already unsubscribed.';
            } else {
                $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute(array((int) $subscriber['id']));
                $success = 'You have been unsubscribed from SSVP South Sudan updates.';
            }
        } catch (Throwable $exception) {
            error_log('Newsletter unsubscribe failed: ' . $exception->getMessage());
            $error = 'The newsletter service is temporarily unavailable.';
        }
    }
}

$pageTitle = 'Newsletter Unsubscribe';
$pageDescription = 'Unsubscribe from SSVP South Sudan newsletter updates.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="newsroom-latest section-reveal">
    <div class="container">
        <div class="newsroom-heading-row">
            <h1>Newsletter Unsubscribe</h1>
            <a href="<?php echo site_url('news.php'); ?>">Back to News <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
        <?php if ($success !== '') : ?>
            <div class="form-alert form-alert--success"><?php echo e($success); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="form-alert form-alert--error"><?php echo e($error); ?></div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
