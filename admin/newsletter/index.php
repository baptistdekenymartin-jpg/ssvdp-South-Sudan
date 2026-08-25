<?php
require_once __DIR__ . '/../includes/communication.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
admin_require_csrf();
$adminTitle = 'Newsletter Subscribers';
$activeNav = 'newsletter';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    if ($id > 0 && $action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute(array($id));
        admin_log('updated', 'newsletter_subscribers', $id, 'Newsletter subscriber deactivated.');
        admin_flash('success', 'Subscriber deactivated.');
    } elseif ($id > 0 && $action === 'reactivate') {
        $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active', subscribed_at = CURRENT_TIMESTAMP, unsubscribed_at = NULL WHERE id = ?");
        $stmt->execute(array($id));
        admin_log('updated', 'newsletter_subscribers', $id, 'Newsletter subscriber reactivated.');
        admin_flash('success', 'Subscriber reactivated.');
    }
    header('Location: ' . admin_url('newsletter/'));
    exit;
}

$activeTotal = admin_phase3_count('active_subscribers');
$search = trim((string) ($_GET['search'] ?? ''));
$status = (string) ($_GET['status'] ?? 'all');
$params = array();
$where = 'WHERE 1=1';
if (in_array($status, array('active','unsubscribed'), true)) { $where .= ' AND status = ?'; $params[] = $status; }
$where .= admin_phase3_search_clause(array('email'), $search, $params);
$stmt = $pdo->prepare("SELECT id, email, status, subscribed_at, unsubscribed_at FROM newsletter_subscribers {$where} ORDER BY subscribed_at DESC, id DESC LIMIT 300");
$stmt->execute($params);
$subscribers = $stmt->fetchAll();
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-table-card">
    <div class="admin-toolbar"><div><h2>Newsletter Subscribers</h2><p class="admin-muted">Total Active: <?php echo e((string) $activeTotal); ?></p></div><a class="admin-button" href="<?php echo admin_url('newsletter/export.php'); ?>">Export Active Subscribers</a></div>
    <div class="admin-toolbar">
        <form class="admin-filters" method="get">
            <input class="admin-input" style="width:230px" name="search" placeholder="Search email" value="<?php echo e($search); ?>">
            <select class="admin-select" name="status" style="width:165px"><?php foreach (array('all'=>'All','active'=>'Active','unsubscribed'=>'Unsubscribed') as $value => $label) : ?><option value="<?php echo e($value); ?>" <?php echo $status === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option><?php endforeach; ?></select>
            <button class="admin-button admin-button--light" type="submit">Filter</button>
        </form>
    </div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Email</th><th>Subscribed</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($subscribers as $subscriber) : ?>
            <tr><td><strong><?php echo e($subscriber['email']); ?></strong></td><td><?php echo e(admin_phase3_date($subscriber['subscribed_at'])); ?></td><td><?php echo admin_phase3_status_badge($subscriber['status']); ?></td><td><div class="admin-row-actions"><form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $subscriber['id']; ?>"><?php if ($subscriber['status'] === 'active') : ?><button name="action" value="deactivate" type="submit">Deactivate</button><?php else : ?><button name="action" value="reactivate" type="submit">Reactivate</button><?php endif; ?></form></div></td></tr>
        <?php endforeach; ?>
        <?php if (!$subscribers) : ?><tr><td colspan="4">No subscribers found.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>