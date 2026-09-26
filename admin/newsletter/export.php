<?php
require_once __DIR__ . '/../includes/communication.php';
require_once __DIR__ . '/../../includes/newsletter.php';

admin_require_permission('newsletter.export');
$pdo = admin_require_db();
ssvdp_newsletter_ensure_schema($pdo);
$stmt = $pdo->prepare("SELECT email, subscribed_at FROM newsletter_subscribers WHERE status = 'active' ORDER BY email ASC");
$stmt->execute();
admin_log('exported', 'newsletter_subscribers', null, 'Active newsletter subscribers exported to CSV.');

$filename = 'ssvp-newsletter-subscribers-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
$out = fopen('php://output', 'w');
fputcsv($out, array('Email', 'Subscribed Date'));
foreach ($stmt as $row) {
    fputcsv($out, array($row['email'], $row['subscribed_at']));
}
fclose($out);
exit;
