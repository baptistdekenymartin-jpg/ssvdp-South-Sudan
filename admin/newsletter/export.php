<?php
require_once __DIR__ . '/../includes/communication.php';
admin_require_auth();
$pdo = admin_require_db();
$stmt = $pdo->query("SELECT email, subscribed_at FROM newsletter_subscribers WHERE status = 'active' ORDER BY email ASC");
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="ssvdp-active-newsletter-subscribers.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, array('Email', 'Subscribed Date'));
foreach ($stmt as $row) {
    fputcsv($out, array($row['email'], $row['subscribed_at']));
}
fclose($out);
exit;