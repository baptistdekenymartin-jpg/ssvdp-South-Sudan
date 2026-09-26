<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_permission('content.manage');
$pdo = admin_require_db();
require_once __DIR__ . '/_shared.php';
featured_activity_ensure_schema($pdo);

$activeNav = 'featured';
$errors = array();
$fallback = $siteConfig['featuredActivity'];
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$activity = featured_activity_empty_activity($fallback);

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM featured_activity WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        admin_flash('error', 'Featured Activity not found.');
        header('Location: ' . admin_url('featured-activity/'));
        exit;
    }
    $activity = featured_activity_activity_from_row($fallback, $row);
    if (!admin_can_edit_content($activity)) { admin_forbidden(); }
}

if ($id <= 0 && !admin_can('content.create')) { admin_forbidden(); }
$adminTitle = $id > 0 ? 'Edit Featured Activity' : 'Add Featured Activity';
$categoryOptions = featured_activity_category_options();
$locationOptions = featured_activity_location_options();
list($categorySelect, $customCategory) = featured_activity_choice_state($activity['category'], $categoryOptions);
list($locationSelect, $customLocation) = featured_activity_choice_state($activity['location'], $locationOptions);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    foreach (array('label','title','activity_date','date_label','participants','description','guests','button_link') as $field) {
        $activity[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $activity['id'] = $id;
    $categorySelect = trim((string) ($_POST['category_select'] ?? ''));
    $customCategory = trim((string) ($_POST['category_custom'] ?? ''));
    $locationSelect = trim((string) ($_POST['location_select'] ?? ''));
    $customLocation = trim((string) ($_POST['location_custom'] ?? ''));
    $activity['category'] = featured_activity_choice_value($categorySelect, $customCategory, $categoryOptions);
    $activity['location'] = featured_activity_choice_value($locationSelect, $customLocation, $locationOptions);
    $action = (string) ($_POST['submit_action'] ?? 'draft');
    $transitionActivity = $id > 0 ? $activity : array('status' => 'draft', 'created_by' => (int) $adminUser['id']);
    $activity['status'] = admin_content_status_for_action($action, $transitionActivity);
    if (!admin_can_transition_content($action, $transitionActivity)) { $errors[] = 'You do not have permission to perform that workflow action.'; }
    $activity['button_label'] = FEATURED_ACTIVITY_BUTTON_LABEL;

    if ($activity['title'] === '') { $errors[] = 'Activity title is required.'; }
    if ($activity['category'] === '') { $errors[] = $categorySelect === 'Others' ? 'Custom category is required.' : 'Category is required.'; }
    if ($activity['location'] === '') { $errors[] = $locationSelect === 'Others' ? 'Custom location is required.' : 'Location is required.'; }
    if ($activity['description'] === '') { $errors[] = 'Description is required.'; }

    if (!empty($_FILES['image']['name'])) {
        try { $activity['image_path'] = admin_store_upload($_FILES['image'], 'featured-activity'); }
        catch (Throwable $e) { $errors[] = $e->getMessage(); }
    }

    if (!$errors) {
        $pdo->beginTransaction();
        try {
            if ($activity['status'] === 'published') {
                $pdo->prepare("UPDATE featured_activity SET status = 'draft' WHERE id <> ? AND status IN ('published','active')")->execute([(int) $activity['id']]);
            }

            if ((int) $activity['id'] > 0) {
                $stmt = $pdo->prepare('UPDATE featured_activity SET label=?, title=?, category=?, activity_date=?, date_label=?, location=?, participants=?, description=?, guests=?, image_path=?, button_label=?, button_link=?, status=?, updated_by=? WHERE id=?');
                $stmt->execute([$activity['label'], $activity['title'], $activity['category'], $activity['activity_date'] ?: null, $activity['date_label'], $activity['location'], $activity['participants'], $activity['description'], $activity['guests'], $activity['image_path'], FEATURED_ACTIVITY_BUTTON_LABEL, $activity['button_link'], $activity['status'], (int) $adminUser['id'], (int) $activity['id']]);
                $savedId = (int) $activity['id'];
                $logAction = $activity['status'] === 'published' ? 'published' : ($activity['status'] === 'pending_review' ? 'submitted_for_review' : 'updated');
                $message = 'Featured Activity updated.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO featured_activity (label, title, category, activity_date, date_label, location, participants, description, guests, image_path, button_label, button_link, status, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$activity['label'], $activity['title'], $activity['category'], $activity['activity_date'] ?: null, $activity['date_label'], $activity['location'], $activity['participants'], $activity['description'], $activity['guests'], $activity['image_path'], FEATURED_ACTIVITY_BUTTON_LABEL, $activity['button_link'], $activity['status'], (int) $adminUser['id']]);
                $savedId = (int) $pdo->lastInsertId();
                $logAction = $activity['status'] === 'published' ? 'published' : ($activity['status'] === 'pending_review' ? 'submitted_for_review' : 'created');
                $message = 'Featured Activity created.';
            }

            $pdo->commit();
            admin_log($logAction, 'featured_activity', $savedId, $message);
            admin_flash('success', $message);
            header('Location: ' . admin_url('featured-activity/'));
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $errors[] = 'Could not save the Featured Activity. Please try again.';
        }
    }
}

require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel">
    <?php foreach ($errors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?>
    <form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $activity['id']; ?>">
        <div class="admin-form-grid"><div class="admin-field"><label>Activity Title</label><input class="admin-input" name="title" value="<?php echo e($activity['title']); ?>" required></div><div class="admin-field"><label>Category</label><select class="admin-select" name="category_select" data-other-select="category_custom" required><option value="">Select category</option><?php foreach ($categoryOptions as $option) : ?><option value="<?php echo e($option); ?>" <?php echo $categorySelect === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option><?php endforeach; ?><option value="Others" <?php echo $categorySelect === 'Others' ? 'selected' : ''; ?>>Others</option></select><div class="admin-field" data-other-field="category_custom" <?php echo $categorySelect === 'Others' ? '' : 'hidden'; ?>><label>Custom Category</label><input class="admin-input" name="category_custom" value="<?php echo e($customCategory); ?>" <?php echo $categorySelect === 'Others' ? 'required' : ''; ?>></div></div></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Date</label><input class="admin-input" type="date" name="activity_date" value="<?php echo e($activity['activity_date']); ?>"><small>Optional structured date.</small></div><div class="admin-field"><label>Display Date</label><input class="admin-input" name="date_label" value="<?php echo e($activity['date_label']); ?>"></div></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Location</label><select class="admin-select" name="location_select" data-other-select="location_custom" required><option value="">Select location</option><?php foreach ($locationOptions as $option) : ?><option value="<?php echo e($option); ?>" <?php echo $locationSelect === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option><?php endforeach; ?><option value="Others" <?php echo $locationSelect === 'Others' ? 'selected' : ''; ?>>Others</option></select><div class="admin-field" data-other-field="location_custom" <?php echo $locationSelect === 'Others' ? '' : 'hidden'; ?>><label>Custom Location</label><input class="admin-input" name="location_custom" value="<?php echo e($customLocation); ?>" <?php echo $locationSelect === 'Others' ? 'required' : ''; ?>></div></div><div class="admin-field"><label>Participants</label><input class="admin-input" name="participants" value="<?php echo e($activity['participants']); ?>"></div></div>
        <div class="admin-field"><label>Description</label><textarea class="admin-textarea" name="description" required><?php echo e($activity['description']); ?></textarea></div>
        <div class="admin-field"><label>Important Guests</label><input class="admin-input" name="guests" value="<?php echo e($activity['guests']); ?>"></div>
        <div class="admin-field"><label>Report Link</label><input class="admin-input" name="button_link" value="<?php echo e($activity['button_link']); ?>"></div>
        <div class="admin-form-grid"><div class="admin-field"><label>Status</label><select class="admin-select" name="status"><option value="draft" <?php echo $activity['status']==='draft'?'selected':''; ?>>Draft</option><option value="pending_review" <?php echo $activity['status']==='pending_review'?'selected':''; ?>>Pending Review</option><option value="published" <?php echo $activity['status']==='published'?'selected':''; ?>>Published</option><option value="archived" <?php echo $activity['status']==='archived'?'selected':''; ?>>Archived</option></select></div><div class="admin-field"><label>Change Image</label><input class="admin-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-selected-image-input><small>Leave Change Image empty to keep the current image.</small></div></div>
        <?php if ($activity['image_path'] !== '') : ?><div class="admin-image-preview"><strong>Current Image</strong><img src="<?php echo site_url($activity['image_path']); ?>" alt=""></div><?php endif; ?>
        <div class="admin-image-preview" data-selected-image-preview hidden><strong>Selected Image Preview</strong><img alt="Selected image preview"></div>
        <input type="hidden" name="label" value="<?php echo e($activity['label']); ?>">
        <div class="admin-actions"><a class="admin-button admin-button--light" href="<?php echo admin_url('featured-activity/'); ?>">Back to List</a><button class="admin-button admin-button--yellow" type="submit" formaction="<?php echo admin_url('featured-activity/preview.php'); ?>" formmethod="post" formenctype="multipart/form-data" formtarget="_blank" formnovalidate>Preview</button><?php foreach (admin_content_allowed_actions($activity) as $workflowAction) : ?><button class="admin-button<?php echo $workflowAction === 'publish' ? '' : ' admin-button--light'; ?>" name="submit_action" value="<?php echo e($workflowAction); ?>" type="submit"><?php echo e(admin_content_action_label($workflowAction)); ?></button><?php endforeach; ?></div>
    </form>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-other-select]').forEach(function (select) {
        var field = document.querySelector('[data-other-field="' + select.getAttribute('data-other-select') + '"]');
        var input = field ? field.querySelector('input') : null;
        function updateOtherField() {
            var isOther = select.value === 'Others' || select.value === 'Other';
            if (field) { field.hidden = !isOther; }
            if (input) { input.required = isOther; }
        }
        select.addEventListener('change', updateOtherField);
        updateOtherField();
    });

    var imageInput = document.querySelector('[data-selected-image-input]');
    var imagePreview = document.querySelector('[data-selected-image-preview]');
    var imagePreviewImg = imagePreview ? imagePreview.querySelector('img') : null;
    var selectedImageUrl = '';

    if (imageInput && imagePreview && imagePreviewImg) {
        imageInput.addEventListener('change', function () {
            if (selectedImageUrl) {
                URL.revokeObjectURL(selectedImageUrl);
                selectedImageUrl = '';
            }

            var file = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
            if (!file) {
                imagePreview.hidden = true;
                imagePreviewImg.removeAttribute('src');
                return;
            }

            selectedImageUrl = URL.createObjectURL(file);
            imagePreviewImg.src = selectedImageUrl;
            imagePreview.hidden = false;
        });
    }
});
</script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>