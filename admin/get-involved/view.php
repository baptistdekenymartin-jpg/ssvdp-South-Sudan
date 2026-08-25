<?php
require_once __DIR__ . '/../includes/communication.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$adminTitle = 'View Get Involved Request';
$activeNav = 'get-involved';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$submission = admin_phase3_require_row($pdo, 'get_involved_submissions', $id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save_notes') {
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $stmt = $pdo->prepare('UPDATE get_involved_submissions SET notes = ? WHERE id = ?');
        $stmt->execute(array($notes ?: null, $id));
        admin_log('updated', 'get_involved_submissions', $id, 'Get Involved admin notes updated.');
        admin_flash('success', 'Admin notes saved.');
    } elseif ($action === 'delete') {
        admin_phase3_delete_row($pdo, 'get_involved_submissions', $id, 'get_involved_submissions', 'Get Involved request permanently deleted.');
        admin_flash('success', 'Request deleted.');
        header('Location: ' . admin_url('get-involved/'));
        exit;
    } else {
        $statuses = array('read' => 'read', 'unread' => 'new', 'contacted' => 'contacted', 'in_progress' => 'in_progress', 'closed' => 'closed', 'archive' => 'archived');
        if (isset($statuses[$action])) {
            admin_phase3_update_status($pdo, 'get_involved_submissions', $id, $statuses[$action], 'get_involved_submissions', 'Get Involved request marked ' . $statuses[$action] . '.');
            admin_flash('success', 'Request updated.');
        }
    }
    header('Location: ' . admin_url('get-involved/view.php?id=' . $id));
    exit;
}

require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel">
    <div class="admin-toolbar"><div><h2><?php echo e($submission['full_name']); ?></h2><p class="admin-muted">Submitted <?php echo e(admin_phase3_date($submission['created_at'])); ?></p></div><a class="admin-button admin-button--light" href="<?php echo admin_url('get-involved/'); ?>">Back to Requests</a></div>
    <div class="admin-external-warning">External submission - treat links, contact details and requests as untrusted.</div>
    <div class="admin-detail-grid">
        <div class="admin-detail-item"><span>Full Name</span><?php echo e($submission['full_name']); ?></div>
        <div class="admin-detail-item"><span>Email</span><?php echo e($submission['email']); ?></div>
        <div class="admin-detail-item"><span>Phone</span><?php echo e($submission['phone'] ?: 'Not supplied'); ?></div>
        <div class="admin-detail-item"><span>Location</span><?php echo e($submission['location'] ?: 'Not supplied'); ?></div>
        <div class="admin-detail-item"><span>Involvement Type</span><?php echo e($submission['involvement_type']); ?></div>
        <div class="admin-detail-item"><span>Areas of Interest</span><?php echo e($submission['areas_of_interest'] ?: 'Not supplied'); ?></div>
        <div class="admin-detail-item"><span>Received Date</span><?php echo e(admin_phase3_date($submission['created_at'])); ?></div>
        <div class="admin-detail-item"><span>Status</span><?php echo admin_phase3_status_badge($submission['status']); ?></div>
    </div>
    <h2>Message</h2><div class="admin-message-body"><?php echo e($submission['message'] ?: 'No message supplied.'); ?></div>
    <div class="admin-actions" style="margin-top:18px">
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button" name="action" value="read" type="submit">Mark Read</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--light" name="action" value="unread" type="submit">Mark Unread</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--yellow" name="action" value="contacted" type="submit">Mark Contacted</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--yellow" name="action" value="in_progress" type="submit">Mark In Progress</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--yellow" name="action" value="closed" type="submit">Mark Closed</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--danger" name="action" value="archive" type="submit">Archive</button></form>
        <form method="post" onsubmit="return confirm('Delete Request?\n\nThis will permanently delete this request.');"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--danger" name="action" value="delete" type="submit">Delete Permanently</button></form>
    </div>
</section>
<section class="admin-panel" style="margin-top:22px">
    <h2>Internal Admin Notes</h2>
    <form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><textarea class="admin-textarea" name="notes" placeholder="Private notes for staff only"><?php echo e($submission['notes'] ?? ''); ?></textarea><button class="admin-button" name="action" value="save_notes" type="submit">Save Notes</button></form>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>