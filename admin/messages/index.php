<?php
require_once __DIR__ . '/../includes/communication.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
admin_require_csrf();
$adminTitle = 'Messages / Enquiries';
$activeNav = 'messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $statuses = array('read' => 'read', 'unread' => 'new', 'contacted' => 'contacted', 'resolved' => 'resolved', 'archive' => 'archived');
    if ($id > 0 && isset($statuses[$action])) {
        admin_phase3_update_status($pdo, 'contact_messages', $id, $statuses[$action], 'contact_messages', 'Contact message status updated.');
        admin_flash('success', 'Message updated.');
    }
    header('Location: ' . admin_url('messages/'));
    exit;
}

$allowedStatuses = array('all','new','read','contacted','resolved','archived');
$search = trim((string) ($_GET['search'] ?? ''));
$status = (string) ($_GET['status'] ?? 'all');
if (!in_array($status, $allowedStatuses, true)) { $status = 'all'; }
$params = array();
$where = 'WHERE 1=1';
if ($status !== 'all') { $where .= ' AND status = ?'; $params[] = $status; }
$where .= admin_phase3_search_clause(array('full_name','email','phone','subject','message'), $search, $params);
$stmt = $pdo->prepare("SELECT id, full_name, email, subject, status, created_at FROM contact_messages {$where} ORDER BY created_at DESC, id DESC LIMIT 200");
$stmt->execute($params);
$messages = $stmt->fetchAll();
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-table-card">
    <div class="admin-toolbar">
        <div><h2>Messages / Enquiries</h2><p class="admin-muted">New messages stay highlighted until staff mark them read.</p></div>
        <form class="admin-filters" method="get">
            <input class="admin-input" style="width:230px" name="search" placeholder="Search messages" value="<?php echo e($search); ?>">
            <select class="admin-select" name="status" style="width:165px">
                <?php foreach (array('all'=>'All','new'=>'New','read'=>'Read','contacted'=>'Contacted','resolved'=>'Resolved','archived'=>'Archived') as $value => $label) : ?>
                    <option value="<?php echo e($value); ?>" <?php echo $status === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="admin-button admin-button--light" type="submit">Filter</button>
        </form>
    </div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Sender</th><th>Subject</th><th>Received</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($messages as $message) : ?>
            <tr class="<?php echo $message['status'] === 'new' ? 'is-new' : ''; ?>"><td><strong><?php echo e($message['full_name']); ?></strong><br><small><?php echo e($message['email']); ?></small></td><td><?php echo e($message['subject']); ?></td><td><?php echo e(admin_phase3_date($message['created_at'])); ?></td><td><?php echo admin_phase3_status_badge($message['status']); ?></td><td><div class="admin-row-actions"><a href="<?php echo admin_url('messages/view.php?id=' . (int) $message['id']); ?>">View</a></div></td></tr>
        <?php endforeach; ?>
        <?php if (!$messages) : ?><tr><td colspan="5">No messages found.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>