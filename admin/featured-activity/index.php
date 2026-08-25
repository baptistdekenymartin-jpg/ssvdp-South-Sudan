<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$activeNav = 'featured';
$adminTitle = 'Featured Activity';
$errors = array();

$fallback = $siteConfig['featuredActivity'];
$stmt = $pdo->query("SELECT * FROM featured_activity ORDER BY status = 'active' DESC, updated_at DESC, id DESC LIMIT 1");
$row = $stmt->fetch() ?: array();
$activity = array(
    'id' => $row['id'] ?? 0,
    'label' => $row['label'] ?? $fallback['label'],
    'title' => $row['title'] ?? $fallback['title'],
    'category' => $row['category'] ?? $fallback['category'],
    'activity_date' => $row['activity_date'] ?? '',
    'date_label' => $row['date_label'] ?? $fallback['date'],
    'location' => $row['location'] ?? $fallback['location'],
    'participants' => $row['participants'] ?? $fallback['participants'],
    'description' => $row['description'] ?? $fallback['excerpt'],
    'guests' => $row['guests'] ?? $fallback['guests'],
    'image_path' => $row['image_path'] ?? 'assets/images/work/women training.jpg',
    'button_label' => $row['button_label'] ?? $fallback['button_label'],
    'button_link' => $row['button_link'] ?? $fallback['button_link'],
    'status' => $row['status'] ?? 'draft'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    foreach (array('label','title','category','activity_date','date_label','location','participants','description','guests','button_label','button_link','status') as $field) {
        $activity[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    if ($activity['title'] === '') { $errors[] = 'Activity title is required.'; }
    if ($activity['description'] === '') { $errors[] = 'Description is required.'; }
    if (!empty($_FILES['image']['name'])) {
        try { $activity['image_path'] = admin_store_upload($_FILES['image'], 'featured-activity'); }
        catch (Throwable $e) { $errors[] = $e->getMessage(); }
    }
    if (!$errors) {
        if ($activity['status'] === 'active') { $pdo->exec("UPDATE featured_activity SET status = 'draft'"); }
        if ((int) $activity['id'] > 0) {
            $stmt = $pdo->prepare('UPDATE featured_activity SET label=?, title=?, category=?, activity_date=?, date_label=?, location=?, participants=?, description=?, guests=?, image_path=?, button_label=?, button_link=?, status=?, updated_by=? WHERE id=?');
            $stmt->execute([$activity['label'], $activity['title'], $activity['category'], $activity['activity_date'] ?: null, $activity['date_label'], $activity['location'], $activity['participants'], $activity['description'], $activity['guests'], $activity['image_path'], $activity['button_label'], $activity['button_link'], $activity['status'] === 'active' ? 'active' : 'draft', (int) $adminUser['id'], (int) $activity['id']]);
            $id = (int) $activity['id'];
        } else {
            $stmt = $pdo->prepare('INSERT INTO featured_activity (label, title, category, activity_date, date_label, location, participants, description, guests, image_path, button_label, button_link, status, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$activity['label'], $activity['title'], $activity['category'], $activity['activity_date'] ?: null, $activity['date_label'], $activity['location'], $activity['participants'], $activity['description'], $activity['guests'], $activity['image_path'], $activity['button_label'], $activity['button_link'], $activity['status'] === 'active' ? 'active' : 'draft', (int) $adminUser['id']]);
            $id = (int) $pdo->lastInsertId();
        }
        admin_log('updated', 'featured_activity', $id, 'Featured Activity updated.');
        admin_flash('success', 'Featured Activity updated.');
        header('Location: ' . admin_url('featured-activity/'));
        exit;
    }
}
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel">
    <?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?>
    <form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
        <div class="admin-form-grid"><div class="admin-field"><label>Activity Title</label><input class="admin-input" name="title" value="<?php echo e($activity['title']); ?>" required></div><div class="admin-field"><label>Category</label><input class="admin-input" name="category" value="<?php echo e($activity['category']); ?>"></div></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Date</label><input class="admin-input" type="date" name="activity_date" value="<?php echo e($activity['activity_date']); ?>"><small>Optional structured date.</small></div><div class="admin-field"><label>Display Date</label><input class="admin-input" name="date_label" value="<?php echo e($activity['date_label']); ?>"></div></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Location</label><input class="admin-input" name="location" value="<?php echo e($activity['location']); ?>"></div><div class="admin-field"><label>Participants</label><input class="admin-input" name="participants" value="<?php echo e($activity['participants']); ?>"></div></div>
        <div class="admin-field"><label>Description</label><textarea class="admin-textarea" name="description" required><?php echo e($activity['description']); ?></textarea></div>
        <div class="admin-field"><label>Important Guests</label><input class="admin-input" name="guests" value="<?php echo e($activity['guests']); ?>"></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Button Label</label><input class="admin-input" name="button_label" value="<?php echo e($activity['button_label']); ?>"></div><div class="admin-field"><label>Report Link</label><input class="admin-input" name="button_link" value="<?php echo e($activity['button_link']); ?>"></div></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Status</label><select class="admin-select" name="status"><option value="draft" <?php echo $activity['status']==='draft'?'selected':''; ?>>Draft</option><option value="active" <?php echo $activity['status']==='active'?'selected':''; ?>>Active</option></select></div><div class="admin-field"><label>Change Image</label><input class="admin-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div></div>
        <?php if ($activity['image_path']) : ?><div class="admin-image-preview"><img src="<?php echo site_url($activity['image_path']); ?>" alt="Current activity image"></div><?php endif; ?>
        <input type="hidden" name="label" value="<?php echo e($activity['label']); ?>">
        <div class="admin-actions"><a class="admin-button admin-button--yellow" href="<?php echo admin_url('featured-activity/preview.php'); ?>" target="_blank">Preview</a><button class="admin-button" type="submit">Update Homepage</button></div>
    </form>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
