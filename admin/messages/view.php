<?php
require_once __DIR__ . '/../includes/communication.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$adminTitle = 'View Message';
$activeNav = 'messages';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$message = admin_phase3_require_row($pdo, 'contact_messages', $id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    $statuses = array('read' => 'read', 'unread' => 'new', 'contacted' => 'contacted', 'resolved' => 'resolved', 'archive' => 'archived');
    if ($action === 'delete') {
        admin_phase3_delete_row($pdo, 'contact_messages', $id, 'contact_messages', 'Contact message permanently deleted.');
        admin_flash('success', 'Message deleted.');
        header('Location: ' . admin_url('messages/'));
        exit;
    }
    if (isset($statuses[$action])) {
        admin_phase3_update_status($pdo, 'contact_messages', $id, $statuses[$action], 'contact_messages', 'Contact message marked ' . $statuses[$action] . '.');
        admin_flash('success', 'Message updated.');
        header('Location: ' . admin_url('messages/view.php?id=' . $id));
        exit;
    }
}

require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-panel">
    <div class="admin-toolbar"><div><h2><?php echo e($message['subject']); ?></h2><p class="admin-muted">Submitted <?php echo e(admin_phase3_date($message['created_at'])); ?></p></div><a class="admin-button admin-button--light" href="<?php echo admin_url('messages/'); ?>">Back to Messages</a></div>
    <div class="admin-external-warning">External submission - treat links, contact details and requests as untrusted.</div>
    <div class="admin-detail-grid">
        <div class="admin-detail-item"><span>Full Name</span><?php echo e($message['full_name']); ?></div>
        <div class="admin-detail-item"><span>Email</span><?php echo e($message['email']); ?></div>
        <div class="admin-detail-item"><span>Phone</span><?php echo e($message['phone'] ?: 'Not supplied'); ?></div>
        <div class="admin-detail-item"><span>Subject</span><?php echo e($message['subject']); ?></div>
        <div class="admin-detail-item"><span>Received Date</span><?php echo e(admin_phase3_date($message['created_at'])); ?></div>
        <div class="admin-detail-item"><span>Current Status</span><?php echo admin_phase3_status_badge($message['status']); ?></div>
    </div>
    <h2>Message</h2>
    <div class="admin-message-body"><?php echo e($message['message']); ?></div>
    <div class="admin-actions" style="margin-top:18px">
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button" name="action" value="read" type="submit">Mark Read</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--light" name="action" value="unread" type="submit">Mark Unread</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--yellow" name="action" value="contacted" type="submit">Mark Contacted</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--yellow" name="action" value="resolved" type="submit">Mark Resolved</button></form>
        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--danger" name="action" value="archive" type="submit">Archive</button></form>
        <form method="post" onsubmit="return confirm('Delete Message?\n\nThis will permanently delete this message.');"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><button class="admin-button admin-button--danger" name="action" value="delete" type="submit">Delete Permanently</button></form>
    </div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>