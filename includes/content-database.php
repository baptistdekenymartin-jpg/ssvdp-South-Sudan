<?php

require_once __DIR__ . '/../config/database.php';

function ssvdp_db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }

    $attempted = true;

    try {
        $pdo = get_database_connection();
    } catch (Throwable $exception) {
        $pdo = null;
    }

    return $pdo;
}

function ssvdp_table_exists(PDO $pdo, string $table): bool
{
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
        $stmt->execute([$table]);
        return (int) $stmt->fetchColumn() > 0;
    } catch (Throwable $exception) {
        return false;
    }
}

function ssvdp_slugify(string $value): string
{
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?: '';
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'item-' . bin2hex(random_bytes(3));
}

function ssvdp_format_date(?string $date, string $fallback = 'To be updated'): string
{
    if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return $fallback;
    }

    $time = strtotime($date);
    return $time ? date('j F Y', $time) : $fallback;
}

function ssvdp_filter_key(?string $category): string
{
    $category = strtolower((string) $category);

    if (str_contains($category, 'emergency') || str_contains($category, 'idp') || str_contains($category, 'refugee')) {
        return 'emergency-support';
    }
    if (str_contains($category, 'training') || str_contains($category, 'skill') || str_contains($category, 'women')) {
        return 'training';
    }
    if (str_contains($category, 'community')) {
        return 'community-activities';
    }
    if (str_contains($category, 'partner')) {
        return 'partnerships';
    }
    if (str_contains($category, 'event')) {
        return 'events';
    }

    return 'programmes';
}

function ssvdp_public_plain_text(?string $value): string
{
    $raw = preg_replace('#<(script|iframe|object|embed|style)\b[^>]*>.*?</\1>#is', '', (string) $value) ?? '';
    $raw = preg_replace('#</?(script|iframe|object|embed|style)\b[^>]*>#i', '', $raw) ?? '';
    $text = html_entity_decode(strip_tags($raw), ENT_QUOTES, 'UTF-8');
    return trim(preg_replace('/\s+/', ' ', $text) ?? '');
}

function ssvdp_public_teaser(?string $value, int $limit = 220): string
{
    $text = ssvdp_public_plain_text($value);
    if ($text === '' || strlen($text) <= $limit) {
        return $text;
    }

    $cut = substr($text, 0, $limit);
    $space = strrpos($cut, ' ');
    if ($space !== false && $space > 80) {
        $cut = substr($cut, 0, $space);
    }

    return rtrim($cut, " \t\n\r\0\x0B.,;:") . '...';
}

function ssvdp_public_url(?string $value, string $fallback = '#'): string
{
    $url = trim(strip_tags((string) $value));
    if ($url === '') {
        return $fallback;
    }

    if (preg_match('#^(https?://|mailto:|tel:|/)#i', $url)) {
        return $url;
    }

    return $fallback;
}

function ssvdp_public_news(array $fallback): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'news')) {
        return $fallback;
    }

    try {
        $stmt = $pdo->query("SELECT id, title, slug, excerpt, content, featured_image, category, location, status, is_featured, published_at, created_at FROM news WHERE status = 'published' AND archived_at IS NULL ORDER BY is_featured DESC, COALESCE(published_at, created_at) DESC, id DESC");
        $rows = $stmt->fetchAll();
    } catch (Throwable $exception) {
        return $fallback;
    }

    if (!$rows) {
        return $fallback;
    }

    return array_map(static function (array $row): array {
        return array(
            'id' => (int) $row['id'],
            'category' => ssvdp_public_plain_text($row['category'] ?: 'Programme Update'),
            'filter' => ssvdp_filter_key($row['category'] ?? ''),
            'title' => ssvdp_public_plain_text($row['title']),
            'description' => ssvdp_public_teaser($row['excerpt'] ?: $row['content'], 240),
            'date' => ssvdp_format_date($row['published_at'] ?: $row['created_at']),
            'image' => $row['featured_image'] ?: 'assets/images/placeholders/activity-placeholder.svg',
            'image_alt' => ssvdp_public_plain_text($row['title']),
            'link' => 'news-detail.php?slug=' . rawurlencode($row['slug']),
            'is_featured' => (int) $row['is_featured'] === 1
        );
    }, $rows);
}

function ssvdp_public_featured_activity(array $fallback): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'featured_activity')) {
        return $fallback;
    }

    try {
        $stmt = $pdo->query("SELECT * FROM featured_activity WHERE status = 'active' ORDER BY updated_at DESC, id DESC LIMIT 1");
        $row = $stmt->fetch();
    } catch (Throwable $exception) {
        return $fallback;
    }

    if (!$row) {
        return $fallback;
    }

    $fallback['label'] = ssvdp_public_plain_text($row['label'] ?: $fallback['label']);
    $fallback['title'] = ssvdp_public_plain_text($row['title'] ?: $fallback['title']);
    $fallback['date'] = ssvdp_public_plain_text($row['date_label'] ?: ssvdp_format_date($row['activity_date'], $fallback['date']));
    $fallback['location'] = ssvdp_public_plain_text($row['location'] ?: $fallback['location']);
    $fallback['participants'] = ssvdp_public_plain_text($row['participants'] ?: $fallback['participants']);
    $fallback['category'] = ssvdp_public_plain_text($row['category'] ?: $fallback['category']);
    $fallback['excerpt'] = ssvdp_public_teaser($row['description'] ?: $fallback['excerpt'], 320);
    $fallback['guests'] = ssvdp_public_teaser($row['guests'] ?: ($fallback['guests'] ?? ''), 180);
    $fallback['image'] = $row['image_path'] ?: ($fallback['image'] ?? 'assets/images/work/women training.jpg');
    $fallback['button_label'] = ssvdp_public_plain_text($row['button_label'] ?: $fallback['button_label']);
    $fallback['button_link'] = ssvdp_public_url($row['button_link'] ?: $fallback['button_link'], $fallback['button_link']);

    return $fallback;
}

function ssvdp_public_gallery_items(): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'gallery_albums') || !ssvdp_table_exists($pdo, 'gallery_photos')) {
        return array();
    }

    try {
        $stmt = $pdo->query("SELECT p.image_path, p.caption, a.title, a.category, a.activity_date, a.location FROM gallery_photos p INNER JOIN gallery_albums a ON a.id = p.album_id WHERE a.status = 'published' ORDER BY COALESCE(a.activity_date, a.created_at) DESC, a.id DESC, p.sort_order ASC, p.id ASC");
        $rows = $stmt->fetchAll();
    } catch (Throwable $exception) {
        return array();
    }

    return array_map(static function (array $row): array {
        $category = $row['category'] ?: 'community-activities';
        return array(
            'image' => $row['image_path'],
            'title' => ssvdp_public_teaser($row['caption'] ?: $row['title'], 120),
            'category' => ssvdp_slugify($category),
            'category_label' => ssvdp_public_plain_text($category),
            'caption' => ssvdp_public_teaser($row['caption'] ?: '', 180),
            'location' => ssvdp_public_plain_text($row['location'] ?: ''),
            'date' => ssvdp_format_date($row['activity_date'], '')
        );
    }, $rows);
}



function ssvdp_public_events(int $limit = 3): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'events')) { return array(); }
    try {
        $stmt = $pdo->prepare("SELECT title, type, short_description, start_date, end_date, start_time, location FROM events WHERE status = 'published' AND start_date >= CURDATE() ORDER BY start_date ASC, start_time ASC, id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(static function (array $row): array {
            $row['title'] = ssvdp_public_plain_text($row['title']);
            $row['type'] = ssvdp_public_plain_text($row['type']);
            $row['short_description'] = ssvdp_public_teaser($row['short_description'], 220);
            $row['location'] = ssvdp_public_plain_text($row['location']);
            return $row;
        }, $stmt->fetchAll());
    } catch (Throwable $exception) { return array(); }
}

function ssvdp_public_programme_updates(int $limit = 3): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'programme_updates')) { return array(); }
    try {
        $stmt = $pdo->prepare("SELECT programme, title, short_description, update_date, location FROM programme_updates WHERE status = 'published' ORDER BY update_date DESC, id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(static function (array $row): array {
            $row['programme'] = ssvdp_public_plain_text($row['programme']);
            $row['title'] = ssvdp_public_plain_text($row['title']);
            $row['short_description'] = ssvdp_public_teaser($row['short_description'], 220);
            $row['location'] = ssvdp_public_plain_text($row['location']);
            return $row;
        }, $stmt->fetchAll());
    } catch (Throwable $exception) { return array(); }
}

function ssvdp_public_impact_updates(int $limit = 3): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'impact_updates')) { return array(); }
    try {
        $stmt = $pdo->prepare("SELECT title, value, unit, programme, description, impact_date FROM impact_updates WHERE status = 'published' ORDER BY impact_date DESC, id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(static function (array $row): array {
            $row['title'] = ssvdp_public_plain_text($row['title']);
            $row['value'] = ssvdp_public_plain_text($row['value']);
            $row['unit'] = ssvdp_public_plain_text($row['unit']);
            $row['programme'] = ssvdp_public_plain_text($row['programme']);
            $row['description'] = ssvdp_public_teaser($row['description'], 220);
            return $row;
        }, $stmt->fetchAll());
    } catch (Throwable $exception) { return array(); }
}

function ssvdp_public_partners(array $fallback): array
{
    $pdo = ssvdp_db();
    if (!$pdo || !ssvdp_table_exists($pdo, 'partners')) { return $fallback; }
    try {
        $rows = $pdo->query("SELECT name, type, logo_path, website_url, description FROM partners WHERE status = 'active' ORDER BY display_order ASC, name ASC")->fetchAll();
    } catch (Throwable $exception) { return $fallback; }
    if (!$rows) { return $fallback; }
    $slotCount = count($fallback);
    $databasePartners = array_map(static function (array $row): array {
        return array('name' => ssvdp_public_plain_text($row['name']), 'logo' => $row['logo_path'] ?: 'assets/images/logo/ssvdp-logo-cutout.png', 'url' => ssvdp_public_url($row['website_url'], '#'), 'type' => ssvdp_public_plain_text($row['type']), 'description' => ssvdp_public_teaser($row['description'] ?: '', 180));
    }, array_slice($rows, 0, $slotCount));
    foreach ($databasePartners as $index => $partner) {
        $fallback[$index] = $partner;
    }
    return $fallback;
}
