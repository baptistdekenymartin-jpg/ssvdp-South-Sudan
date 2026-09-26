<?php

const FEATURED_ACTIVITY_BUTTON_LABEL = 'Read Full Activity Report';

function featured_activity_category_options(): array
{
    return array(
        'Vocational Training',
        'Income Generating Activities',
        'Agriculture / Farming',
        'Poultry',
        'Community Empowerment',
        'Humanitarian Support',
        'Youth Activities'
    );
}

function featured_activity_location_options(): array
{
    return admin_content_locations();
}

function featured_activity_choice_value(string $selected, string $custom, array $options): string
{
    $selected = ssvdp_public_plain_text($selected);
    if ($selected === 'Others' || $selected === 'Other') {
        return ssvdp_public_plain_text($custom);
    }
    return in_array($selected, $options, true) ? $selected : '';
}

function featured_activity_choice_state(string $value, array $options): array
{
    $value = ssvdp_public_plain_text($value);
    if ($value !== '' && in_array($value, $options, true)) {
        return array($value, '');
    }
    return array('Others', $value);
}

function featured_activity_normalize_status(?string $status): string
{
    $status = strtolower(trim((string) $status));
    if ($status === 'active' || $status === 'published') {
        return 'published';
    }
    if ($status === 'archived') {
        return 'archived';
    }
    return 'draft';
}

function featured_activity_status_label(?string $status): string
{
    return ucfirst(featured_activity_normalize_status($status));
}

function featured_activity_status_class(?string $status): string
{
    return featured_activity_normalize_status($status);
}

function featured_activity_ensure_schema(PDO $pdo): void
{
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM featured_activity LIKE 'status'");
        $column = $stmt ? $stmt->fetch() : null;
        $type = strtolower((string) ($column['Type'] ?? ''));
        if ($type !== '' && (!str_contains($type, 'pending_review') || !str_contains($type, 'published') || !str_contains($type, 'archived'))) {
            $pdo->exec("ALTER TABLE featured_activity MODIFY status ENUM('draft','pending_review','published','archived','active') NOT NULL DEFAULT 'draft'");
        }
        $pdo->exec("UPDATE featured_activity SET status = 'published' WHERE status = 'active'");
    } catch (Throwable $exception) {
        // Older installs can still be read; writes will surface normal database errors if schema migration is unavailable.
    }
}

function featured_activity_empty_activity(array $fallback): array
{
    return array(
        'id' => 0,
        'label' => $fallback['label'],
        'title' => '',
        'category' => '',
        'activity_date' => '',
        'date_label' => '',
        'location' => '',
        'participants' => '',
        'description' => '',
        'guests' => '',
        'image_path' => '',
        'button_label' => FEATURED_ACTIVITY_BUTTON_LABEL,
        'button_link' => $fallback['button_link'],
        'status' => 'draft'
    );
}

function featured_activity_activity_from_row(array $fallback, array $row): array
{
    return array(
        'id' => (int) ($row['id'] ?? 0),
        'label' => $row['label'] ?? $fallback['label'],
        'title' => $row['title'] ?? '',
        'category' => $row['category'] ?? '',
        'activity_date' => $row['activity_date'] ?? '',
        'date_label' => $row['date_label'] ?? '',
        'location' => $row['location'] ?? '',
        'participants' => $row['participants'] ?? '',
        'description' => $row['description'] ?? '',
        'guests' => $row['guests'] ?? '',
        'image_path' => $row['image_path'] ?? '',
        'button_label' => FEATURED_ACTIVITY_BUTTON_LABEL,
        'button_link' => $row['button_link'] ?? $fallback['button_link'],
        'status' => featured_activity_normalize_status($row['status'] ?? 'draft')
    );
}

function featured_activity_preview_image_src(string $image): string
{
    if (str_starts_with($image, 'data:image/')) {
        return $image;
    }
    return site_url($image);
}