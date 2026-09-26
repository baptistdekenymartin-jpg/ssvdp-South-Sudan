<?php
require_once __DIR__ . '/../includes/auth.php';
admin_require_auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
}
require_once __DIR__ . '/_shared.php';

function featured_activity_preview_to_public(array $fallback, array $activity): array
{
    $row = array(
        'label' => $activity['label'] ?? '',
        'title' => $activity['title'] ?? '',
        'date_label' => $activity['date_label'] ?? '',
        'activity_date' => $activity['activity_date'] ?? '',
        'location' => $activity['location'] ?? '',
        'participants' => $activity['participants'] ?? '',
        'category' => $activity['category'] ?? '',
        'description' => $activity['description'] ?? '',
        'guests' => $activity['guests'] ?? '',
        'image_path' => $activity['image_path'] ?? '',
        'button_link' => $activity['button_link'] ?? ''
    );
    return ssvdp_featured_activity_from_row($fallback, $row);
}

$fallback = $siteConfig['featuredActivity'];
$activity = array(
    'label' => $fallback['label'],
    'title' => $fallback['title'],
    'date' => $fallback['date'],
    'location' => $fallback['location'],
    'participants' => $fallback['participants'],
    'category' => $fallback['category'],
    'excerpt' => $fallback['excerpt'],
    'guests' => $fallback['guests'] ?? '',
    'image' => $fallback['image'] ?? 'assets/images/work/women training.jpg',
    'button_label' => FEATURED_ACTIVITY_BUTTON_LABEL,
    'button_link' => $fallback['button_link'],
    'status' => 'fallback',
);
$previewNotice = 'Preview only. This page shows Featured Activity content without changing its status.';
$hasRecord = false;

$pdo = admin_db();
if ($pdo && ssvdp_table_exists($pdo, 'featured_activity')) {
    featured_activity_ensure_schema($pdo);
    try {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('SELECT * FROM featured_activity WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->query("SELECT * FROM featured_activity ORDER BY FIELD(status, 'published', 'active', 'draft', 'archived'), updated_at DESC, id DESC LIMIT 1");
        }
        $row = $stmt ? $stmt->fetch() : false;
        if ($row) {
            $hasRecord = true;
            $activity = ssvdp_featured_activity_from_row($fallback, $row);
            $activity['status'] = featured_activity_normalize_status($row['status'] ?? 'draft');
            if ($activity['status'] !== 'published') {
                $previewNotice .= ' This item is ' . strtolower(featured_activity_status_label($activity['status'])) . ' and will not appear publicly unless published.';
            }
        } elseif ($id > 0) {
            $previewNotice .= ' The selected Featured Activity could not be found, so fallback homepage content is shown.';
        } else {
            $previewNotice .= ' No Featured Activity record exists yet, so fallback homepage content is shown.';
        }
    } catch (Throwable $exception) {
        $previewNotice .= ' The Featured Activity record could not be loaded, so fallback homepage content is shown.';
    }
} else {
    $previewNotice .= ' The Featured Activity table is not available, so fallback homepage content is shown.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryOptions = featured_activity_category_options();
    $locationOptions = featured_activity_location_options();
    $category = featured_activity_choice_value((string) ($_POST['category_select'] ?? ''), (string) ($_POST['category_custom'] ?? ''), $categoryOptions);
    $location = featured_activity_choice_value((string) ($_POST['location_select'] ?? ''), (string) ($_POST['location_custom'] ?? ''), $locationOptions);

    $formActivity = array(
        'label' => ssvdp_public_plain_text($_POST['label'] ?? $fallback['label']),
        'title' => ssvdp_public_plain_text($_POST['title'] ?? ''),
        'date_label' => ssvdp_public_plain_text($_POST['date_label'] ?? ''),
        'activity_date' => trim((string) ($_POST['activity_date'] ?? '')),
        'location' => $location,
        'participants' => ssvdp_public_plain_text($_POST['participants'] ?? ''),
        'category' => $category,
        'description' => ssvdp_public_teaser($_POST['description'] ?? '', 320),
        'guests' => ssvdp_public_teaser($_POST['guests'] ?? '', 180),
        'image_path' => '',
        'button_link' => ssvdp_public_url($_POST['button_link'] ?? $fallback['button_link'], $fallback['button_link'])
    );
    $activity = featured_activity_preview_to_public($fallback, $formActivity);
    $activity['status'] = featured_activity_normalize_status($_POST['status'] ?? 'draft');
    $previewNotice = 'Preview only. This page shows the current unsaved Featured Activity form values and does not publish changes.';

    if (!empty($_FILES['image']['name'])) {
        try {
            admin_validate_upload($_FILES['image']);
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
            $activity['image'] = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($_FILES['image']['tmp_name']));
        } catch (Throwable $exception) {
            $previewNotice .= ' The selected image could not be previewed: ' . $exception->getMessage();
        }
    } elseif (!empty($_POST['id'])) {
        try {
            $stmt = $pdo ? $pdo->prepare('SELECT image_path FROM featured_activity WHERE id = ? LIMIT 1') : null;
            if ($stmt) {
                $stmt->execute([(int) $_POST['id']]);
                $imagePath = (string) $stmt->fetchColumn();
                if ($imagePath !== '') {
                    $activity['image'] = $imagePath;
                }
            }
        } catch (Throwable $exception) {}
    }
}

$styleVersion = (string) (@filemtime(__DIR__ . '/../../assets/css/style.css') ?: '1');
$responsiveVersion = (string) (@filemtime(__DIR__ . '/../../assets/css/responsive.css') ?: '1');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Featured Activity Preview</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/style.css') . '?v=' . rawurlencode($styleVersion); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/responsive.css') . '?v=' . rawurlencode($responsiveVersion); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>">
</head>
<body>
    <div class="admin-alert" style="margin:18px"><?php echo e($previewNotice); ?></div>
    <?php if (!$hasRecord && $_SERVER['REQUEST_METHOD'] !== 'POST') : ?><div class="admin-alert admin-alert--error" style="margin:0 18px 18px">No saved Featured Activity record was found in the database.</div><?php endif; ?>
    <section class="content-section featured-activity section-reveal is-visible" id="featured-activity">
        <div class="container featured-activity-card featured-grid">
            <div class="activity-media activity-photo">
                <img src="<?php echo e(featured_activity_preview_image_src($activity['image'])); ?>" alt="<?php echo e($activity['title']); ?>" loading="lazy" width="560" height="430">
                <div class="activity-image-badge" aria-label="Activity focus">
                    <span><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                    <p>Building skills.<br>Strengthening families.<br>Empowering communities.</p>
                </div>
            </div>
            <div class="activity-report">
                <p class="activity-label"><span><i class="bi bi-star-fill" aria-hidden="true"></i></span><?php echo e($activity['label']); ?></p>
                <h2><?php echo str_replace(array(' in ', ' and Family'), array(' in<br>', ' and<br>Family'), e($activity['title'])); ?></h2>
                <span class="activity-title-rule" aria-hidden="true"></span>
                <dl class="activity-details">
                    <div>
                        <dt><i class="bi bi-calendar3" aria-hidden="true"></i><span>Date</span></dt>
                        <dd><?php echo e($activity['date']); ?></dd>
                    </div>
                    <div>
                        <dt><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Location</span></dt>
                        <dd><?php echo e($activity['location']); ?></dd>
                    </div>
                    <div>
                        <dt><i class="bi bi-people" aria-hidden="true"></i><span>Participants</span></dt>
                        <dd><?php echo e($activity['participants']); ?></dd>
                    </div>
                    <div>
                        <dt><i class="bi bi-tag" aria-hidden="true"></i><span>Category</span></dt>
                        <dd><?php echo e($activity['category']); ?></dd>
                    </div>
                </dl>
                <p><?php echo e($activity['excerpt']); ?></p>
                <?php if ($activity['guests'] !== '') : ?><p class="activity-guests"><strong>Important guests:</strong> <?php echo e($activity['guests']); ?></p><?php endif; ?>
                <?php if ($activity['button_label'] !== '' && $activity['button_link'] !== '') : ?><a class="btn activity-report-button" href="<?php echo site_url($activity['button_link']); ?>"><?php echo e($activity['button_label']); ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>