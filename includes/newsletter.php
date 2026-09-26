<?php

declare(strict_types=1);

require_once __DIR__ . '/content-database.php';

function ssvdp_newsletter_normalize_email(?string $email): string
{
    return strtolower(trim((string) $email));
}

function ssvdp_newsletter_new_token(): string
{
    return bin2hex(random_bytes(32));
}

function ssvdp_newsletter_ensure_schema(PDO $pdo): void
{
    if (!ssvdp_table_exists($pdo, 'newsletter_subscribers')) {
        return;
    }

    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM newsletter_subscribers LIKE 'unsubscribe_token'");
        $hasToken = $stmt && $stmt->fetch();
        if (!$hasToken) {
            $pdo->exec("ALTER TABLE newsletter_subscribers ADD unsubscribe_token VARCHAR(64) NULL AFTER unsubscribed_at, ADD UNIQUE KEY uniq_newsletter_unsubscribe_token (unsubscribe_token)");
        }
    } catch (Throwable $exception) {
        error_log('Newsletter schema check failed: ' . $exception->getMessage());
    }

    try {
        $stmt = $pdo->query("SELECT id FROM newsletter_subscribers WHERE unsubscribe_token IS NULL OR unsubscribe_token = ''");
        $update = $pdo->prepare('UPDATE newsletter_subscribers SET unsubscribe_token = ? WHERE id = ?');
        foreach ($stmt->fetchAll() as $row) {
            $update->execute(array(ssvdp_newsletter_new_token(), (int) $row['id']));
        }
    } catch (Throwable $exception) {
        error_log('Newsletter token backfill failed: ' . $exception->getMessage());
    }
}

function ssvdp_newsletter_token_for_save(PDO $pdo, ?string $currentToken = null): string
{
    $currentToken = trim((string) $currentToken);
    if ($currentToken !== '') {
        return $currentToken;
    }

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $token = ssvdp_newsletter_new_token();
        try {
            $stmt = $pdo->prepare('SELECT id FROM newsletter_subscribers WHERE unsubscribe_token = ? LIMIT 1');
            $stmt->execute(array($token));
            if (!$stmt->fetch()) {
                return $token;
            }
        } catch (Throwable $exception) {
            return $token;
        }
    }

    return ssvdp_newsletter_new_token();
}

function ssvdp_newsletter_counts(PDO $pdo): array
{
    $counts = array('active' => 0, 'unsubscribed' => 0, 'total' => 0);
    if (!ssvdp_table_exists($pdo, 'newsletter_subscribers')) {
        return $counts;
    }

    try {
        $rows = $pdo->query('SELECT status, COUNT(*) AS total FROM newsletter_subscribers GROUP BY status')->fetchAll();
        foreach ($rows as $row) {
            $status = (string) $row['status'];
            if (array_key_exists($status, $counts)) {
                $counts[$status] = (int) $row['total'];
            }
        }
        $counts['total'] = $counts['active'] + $counts['unsubscribed'];
    } catch (Throwable $exception) {
        return $counts;
    }

    return $counts;
}
