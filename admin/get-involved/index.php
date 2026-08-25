<?php
require_once __DIR__ . '/../includes/communication.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
admin_require_csrf();
$adminTitle = 'Get Involved Requests';
$activeNav = 'get-involved';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $statuses = array('read' => 'read', 'unread' => 'new', 'contacted' => 'contacted', 'in_progress' => 'in_progress', 'closed' => 'closed', 'archive' => 'archived');
    if ($id > 0 && isset($statuses[$action])) {
        admin_phase3_update_status($pdo, 'get_involved_submissions', $id, $statuses[$action], 'get_involved_submissions', 'Get Involved request status updated.');
        admin_flash('success', 'Request updated.');
    }
    header('Location: ' . admin_url('get-involved/'));
    exit;
}

$summary = array('new' => 0, 'read' => 0, 'contacted' => 0, 'in_progress' => 0, 'closed' => 0);
foreach ($pdo->query("SELECT status, COUNT(*) total FROM get_involved_submissions WHERE status <> 'archived' GROUP BY status") as $row) { if (isset($summary[$row['status']])) { $summary[$row['status']] = (int) $row['total']; } }
$allowedStatuses = array('all','new','read','contacted','in_progress','closed','archived');
$search = trim((string) ($_GET['search'] ?? ''));
$status = (string) ($_GET['status'] ?? 'all');
if (!in_array($status, $allowedStatuses, true)) { $status = 'all'; }
$params = array();
$where = 'WHERE 1=1';
if ($status !== 'all') { $where .= ' AND status = ?'; $params[] = $status; }
$where .= admin_phase3_search_clause(array('full_name','email','phone','location','involvement_type','areas_of_interest','message'), $search, $params);
$stmt = $pdo->prepare("SELECT id, full_name, involvement_type, areas_of_interest, status, created_at FROM get_involved_submissions {$where} ORDER BY created_at DESC, id DESC LIMIT 200");
$stmt->execute($params);
$submissions = $stmt->fetchAll();
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-table-card">
    <div class="admin-toolbar"><div><h2>Get Involved Requests</h2><p class="admin-muted">Manage volunteer, partner and support enquiries.</p></div></div>
    <div class="admin-summary-strip"><span class="admin-summary-pill">New: <?php echo e((string) $summary['new']); ?></span><span class="admin-summary-pill">Read: <?php echo e((string) $summary['read']); ?></span><span class="admin-summary-pill">Contacted: <?php echo e((string) $summary['contacted']); ?></span><span class="admin-summary-pill">In Progress: <?php echo e((string) $summary['in_progress']); ?></span><span class="admin-summary-pill">Closed: <?php echo e((string) $summary['closed']); ?></span></div>
    <div class="admin-toolbar">
        <form class="admin-filters" method="get">
            <input class="admin-input" style="width:230px" name="search" placeholder="Search requests" value="<?php echo e($search); ?>">
            <select class="admin-select" name="status" style="width:165px"><?php foreach (array('all'=>'All','new'=>'New','read'=>'Read','contacted'=>'Contacted','in_progress'=>'In Progress','closed'=>'Closed','archived'=>'Archived') as $value => $label) : ?><option value="<?php echo e($value); ?>" <?php echo $status === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option><?php endforeach; ?></select>
            <button class="admin-button admin-button--light" type="submit">Filter</button>
        </form>
    </div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Involvement Type</th><th>Areas of Interest</th><th>Received</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($submissions as $submission) : ?>
            <tr class="<?php echo $submission['status'] === 'new' ? 'is-new' : ''; ?>"><td><strong><?php echo e($submission['full_name']); ?></strong></td><td><?php echo e($submission['involvement_type']); ?></td><td><?php echo e($submission['areas_of_interest'] ?: 'Not supplied'); ?></td><td><?php echo e(admin_phase3_date($submission['created_at'])); ?></td><td><?php echo admin_phase3_status_badge($submission['status']); ?></td><td><div class="admin-row-actions"><a href="<?php echo admin_url('get-involved/view.php?id=' . (int) $submission['id']); ?>">View</a></div></td></tr>
        <?php endforeach; ?>
        <?php if (!$submissions) : ?><tr><td colspan="6">No requests found.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>