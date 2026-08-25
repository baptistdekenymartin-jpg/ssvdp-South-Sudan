<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function admin_phase3_allowed_tables(): array
{
    return array('contact_messages', 'get_involved_submissions', 'newsletter_subscribers');
}

function admin_phase3_require_private_table(string $table): void
{
    if (!in_array($table, admin_phase3_allowed_tables(), true)) {
        admin_forbidden();
    }
}

function admin_phase3_count(string $type): int
{
    $pdo = admin_db();
    if (!$pdo) { return 0; }
    try {
        if ($type === 'new_messages' && ssvdp_table_exists($pdo, 'contact_messages')) {
            return (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
        }
        if ($type === 'new_involved' && ssvdp_table_exists($pdo, 'get_involved_submissions')) {
            return (int) $pdo->query("SELECT COUNT(*) FROM get_involved_submissions WHERE status = 'new'")->fetchColumn();
        }
        if ($type === 'active_subscribers' && ssvdp_table_exists($pdo, 'newsletter_subscribers')) {
            return (int) $pdo->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'")->fetchColumn();
        }
    } catch (Throwable $exception) {
        return 0;
    }
    return 0;
}

function admin_phase3_badge(int $count): string
{
    return $count > 0 ? '<span class="admin-nav-badge">' . e((string) $count) . '</span>' : '';
}

function admin_phase3_search_clause(array $columns, string $search, array &$params): string
{
    if ($search === '') { return ''; }
    $like = '%' . $search . '%';
    $parts = array();
    foreach ($columns as $column) {
        if (!preg_match('/^[a-z_]+$/', $column)) { continue; }
        $parts[] = $column . ' LIKE ?';
        $params[] = $like;
    }
    return $parts ? ' AND (' . implode(' OR ', $parts) . ')' : '';
}

function admin_phase3_date(?string $date): string
{
    if (!$date) { return ''; }
    $time = strtotime($date);
    return $time ? date('j M Y, g:i A', $time) : $date;
}

function admin_phase3_status_badge(string $status): string
{
    return '<span class="admin-status admin-status--' . e($status) . '">' . e(ucfirst(str_replace('_', ' ', $status))) . '</span>';
}

function admin_phase3_require_row(PDO $pdo, string $table, int $id): array
{
    admin_phase3_require_private_table($table);
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ? LIMIT 1");
    $stmt->execute(array($id));
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Record not found.');
    }
    return $row;
}

function admin_phase3_update_status(PDO $pdo, string $table, int $id, string $status, string $entityType, string $description): void
{
    admin_phase3_require_private_table($table);
    $stmt = $pdo->prepare("UPDATE {$table} SET status = ? WHERE id = ?");
    $stmt->execute(array($status, $id));
    admin_log('updated', $entityType, $id, $description);
}

function admin_phase3_delete_row(PDO $pdo, string $table, int $id, string $entityType, string $description): void
{
    admin_phase3_require_private_table($table);
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ? LIMIT 1");
    $stmt->execute(array($id));
    admin_log('deleted', $entityType, $id, $description);
}