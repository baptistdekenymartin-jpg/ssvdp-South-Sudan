<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_permission('content.manage');
$pdo = admin_require_db();
require_once __DIR__ . '/_shared.php';
featured_activity_ensure_schema($pdo);

$adminTitle = 'Featured Activity';
$activeNav = 'featured';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    if ($id > 0) {
        if (in_array($action, array('publish','unpublish','archive','return_draft','submit_review'), true)) {
            $stmt = $pdo->prepare('SELECT * FROM featured_activity WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $targetActivity = $stmt->fetch();
            if (!$targetActivity) { admin_forbidden(); }
            $targetActivity['status'] = featured_activity_normalize_status($targetActivity['status'] ?? 'draft');
            if (!admin_can_transition_content($action, $targetActivity)) { admin_forbidden(); }
            $newStatus = admin_content_status_for_action($action, $targetActivity);
            $pdo->beginTransaction();
            try {
                if ($newStatus === 'published') { $pdo->prepare("UPDATE featured_activity SET status = 'draft' WHERE id <> ? AND status IN ('published','active')")->execute([$id]); }
                $pdo->prepare('UPDATE featured_activity SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
                $pdo->commit();
                admin_log($action, 'featured_activity', $id, 'Featured Activity workflow status changed.');
                admin_flash('success', 'Featured Activity updated.');
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                admin_flash('error', 'Could not update the Featured Activity.');
            }        } elseif ($action === 'delete') {
            if (!admin_is_administrator($adminUser)) {
                admin_forbidden();
            }

            $stmt = $pdo->prepare('SELECT id, title FROM featured_activity WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $activityToDelete = $stmt->fetch();

            if ($activityToDelete) {
                $deleteStmt = $pdo->prepare('DELETE FROM featured_activity WHERE id = ?');
                $deleteStmt->execute([$id]);
                admin_log('deleted', 'featured_activity', $id, 'Featured Activity deleted permanently: ' . (string) $activityToDelete['title']);
                admin_flash('success', 'Featured Activity deleted successfully.');
            }
        }
    }

    header('Location: ' . admin_url('featured-activity/'));
    exit;
}

$status = trim((string) ($_GET['status'] ?? ''));
$allowedStatuses = array('draft', 'published', 'archived');
$params = array();
$where = array('1=1');
if (in_array($status, $allowedStatuses, true)) {
    if ($status === 'published') {
        $where[] = "status IN ('published','active')";
    } else {
        $where[] = 'status = ?';
        $params[] = $status;
    }
}

$stmt = $pdo->prepare('SELECT * FROM featured_activity WHERE ' . implode(' AND ', $where) . ' ORDER BY FIELD(status, \'published\', \'active\', \'draft\', \'archived\'), updated_at DESC, id DESC');
$stmt->execute($params);
$activities = $stmt->fetchAll();

require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar">
    <form class="admin-filters" method="get">
        <select class="admin-select" style="width:170px" name="status"><option value="">All statuses</option><option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option><option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option><option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option></select>
        <button class="admin-button admin-button--light" type="submit">Filter</button>
    </form>
    <a class="admin-button" href="<?php echo admin_url('featured-activity/add.php'); ?>"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add Featured Activity</a>
</div>
<section class="admin-table-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Thumbnail</th><th>Title</th><th>Category</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($activities as $activity) : $normalizedStatus = featured_activity_normalize_status($activity['status'] ?? 'draft'); ?>
                <tr>
                    <td><?php if (!empty($activity['image_path'])) : ?><img class="admin-thumb" src="<?php echo site_url($activity['image_path']); ?>" alt=""><?php endif; ?></td>
                    <td><strong><?php echo e($activity['title']); ?></strong></td>
                    <td><?php echo e($activity['category']); ?></td>
                    <td><?php echo e($activity['date_label'] ?: ssvdp_format_date($activity['activity_date'], '')); ?></td>
                    <td><span class="admin-status admin-status--<?php echo e(featured_activity_status_class($activity['status'] ?? 'draft')); ?>"><?php echo e(featured_activity_status_label($activity['status'] ?? 'draft')); ?></span></td>
                    <td><div class="admin-row-actions">
                        <a href="<?php echo admin_url('featured-activity/edit.php?id=' . (int) $activity['id']); ?>">Edit</a>
                        <a href="<?php echo admin_url('featured-activity/preview.php?id=' . (int) $activity['id']); ?>" target="_blank">Preview</a>
                        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $activity['id']; ?>"><input type="hidden" name="action" value="<?php echo $normalizedStatus === 'published' ? 'unpublish' : 'publish'; ?>"><button type="submit"><?php echo $normalizedStatus === 'published' ? 'Unpublish' : 'Publish'; ?></button></form>
                        <form method="post" onsubmit="return confirm('Archive this Featured Activity? It will no longer appear publicly.');"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $activity['id']; ?>"><input type="hidden" name="action" value="archive"><button type="submit">Archive</button></form>
                        <?php if (admin_is_administrator($adminUser)) : ?><button class="admin-row-delete-button" type="button" data-featured-delete-trigger data-featured-id="<?php echo (int) $activity['id']; ?>" data-featured-title="<?php echo e($activity['title']); ?>">Delete</button><?php endif; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$activities) : ?><tr><td colspan="6">No Featured Activities found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<dialog class="admin-delete-dialog" data-featured-delete-dialog>
    <form method="post" data-featured-delete-form>
        <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="">
        <h2>Delete Featured Activity</h2>
        <p>Are you sure you want to delete this Featured Activity?</p>
        <p><strong data-featured-delete-title></strong></p>
        <div class="admin-delete-dialog__actions">
            <button class="admin-button admin-button--light" type="button" data-featured-delete-cancel>Cancel</button>
            <button class="admin-button admin-button--danger" type="submit">Delete Permanently</button>
        </div>
    </form>
</dialog>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dialog = document.querySelector('[data-featured-delete-dialog]');
    if (!dialog) { return; }

    var form = dialog.querySelector('[data-featured-delete-form]');
    var idInput = form.querySelector('input[name="id"]');
    var titleNode = dialog.querySelector('[data-featured-delete-title]');
    var cancelButton = dialog.querySelector('[data-featured-delete-cancel]');

    document.querySelectorAll('[data-featured-delete-trigger]').forEach(function (button) {
        button.addEventListener('click', function () {
            idInput.value = button.getAttribute('data-featured-id') || '';
            titleNode.textContent = button.getAttribute('data-featured-title') || '';
            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            } else if (window.confirm('Are you sure you want to delete this Featured Activity?\n\n' + titleNode.textContent)) {
                form.submit();
            }
        });
    });

    cancelButton.addEventListener('click', function () {
        dialog.close();
    });
});
</script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
