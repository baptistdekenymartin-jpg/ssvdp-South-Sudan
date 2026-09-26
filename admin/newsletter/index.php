<?php
require_once __DIR__ . '/../includes/communication.php';
require_once __DIR__ . '/../../includes/newsletter.php';

$adminUser = admin_require_permission('communications.manage');
$pdo = admin_require_db();
ssvdp_newsletter_ensure_schema($pdo);
$adminTitle = 'Newsletter Subscribers';
$activeNav = 'newsletter';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    if ($id > 0 && in_array($action, array('unsubscribe','reactivate','delete'), true)) {
        if (!admin_can_newsletter_action($action, $adminUser)) { admin_forbidden(); }
        $stmt = $pdo->prepare('SELECT id, email, status FROM newsletter_subscribers WHERE id = ? LIMIT 1');
        $stmt->execute(array($id));
        $subscriber = $stmt->fetch();

        if ($subscriber) {
            if ($action === 'unsubscribe' && $subscriber['status'] === 'active') {
                $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute(array($id));
                admin_log('updated', 'newsletter_subscribers', $id, 'Newsletter subscriber unsubscribed by staff: ' . $subscriber['email']);
                admin_flash('success', 'Subscriber unsubscribed.');
            } elseif ($action === 'reactivate' && $subscriber['status'] === 'unsubscribed') {
                $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active', subscribed_at = CURRENT_TIMESTAMP, unsubscribed_at = NULL, unsubscribe_token = COALESCE(NULLIF(unsubscribe_token, ''), ?) WHERE id = ?");
                $stmt->execute(array(ssvdp_newsletter_token_for_save($pdo), $id));
                admin_log('updated', 'newsletter_subscribers', $id, 'Newsletter subscriber reactivated by staff: ' . $subscriber['email']);
                admin_flash('success', 'Subscriber reactivated.');
            } elseif ($action === 'delete' && admin_is_administrator($adminUser)) {
                $stmt = $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id = ? LIMIT 1');
                $stmt->execute(array($id));
                admin_log('deleted', 'newsletter_subscribers', $id, 'Newsletter subscriber permanently deleted: ' . $subscriber['email']);
                admin_flash('success', 'Subscriber deleted permanently.');
            }
        }
    }

    header('Location: ' . admin_url('newsletter/'));
    exit;
}

$counts = ssvdp_newsletter_counts($pdo);
$search = ssvdp_newsletter_normalize_email($_GET['search'] ?? '');
$status = (string) ($_GET['status'] ?? 'all');
$params = array();
$where = 'WHERE 1=1';
if (in_array($status, array('active','unsubscribed'), true)) {
    $where .= ' AND status = ?';
    $params[] = $status;
}
$where .= admin_phase3_search_clause(array('email'), $search, $params);
$stmt = $pdo->prepare("SELECT id, email, status, subscribed_at, unsubscribed_at FROM newsletter_subscribers {$where} ORDER BY subscribed_at DESC, id DESC LIMIT 300");
$stmt->execute($params);
$subscribers = $stmt->fetchAll();
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-table-card">
    <div class="admin-toolbar">
        <div>
            <h2>Newsletter Subscribers</h2>
            <p class="admin-muted">Total Active: <?php echo e((string) $counts['active']); ?> | Total Unsubscribed: <?php echo e((string) $counts['unsubscribed']); ?> | Total Subscribers: <?php echo e((string) $counts['total']); ?></p>
        </div>
        <?php if (admin_can_newsletter_action('export', $adminUser)) : ?><a class="admin-button" href="<?php echo admin_url('newsletter/export.php'); ?>">Export Active Subscribers</a><?php endif; ?>
    </div>
    <div class="admin-toolbar">
        <form class="admin-filters" method="get">
            <input class="admin-input" style="width:230px" name="search" placeholder="Search email" value="<?php echo e($search); ?>">
            <select class="admin-select" name="status" style="width:165px">
                <?php foreach (array('all'=>'All','active'=>'Active','unsubscribed'=>'Unsubscribed') as $value => $label) : ?>
                    <option value="<?php echo e($value); ?>" <?php echo $status === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="admin-button admin-button--light" type="submit">Filter</button>
        </form>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Email</th><th>Subscribed Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($subscribers as $subscriber) : ?>
                    <tr>
                        <td><strong><?php echo e($subscriber['email']); ?></strong></td>
                        <td><?php echo e(admin_phase3_date($subscriber['subscribed_at'])); ?></td>
                        <td><?php echo admin_phase3_status_badge($subscriber['status']); ?></td>
                        <td>
                            <div class="admin-row-actions">
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $subscriber['id']; ?>">
                                    <?php if ($subscriber['status'] === 'active') : ?>
                                        <button name="action" value="unsubscribe" type="submit">Unsubscribe</button>
                                    <?php else : ?>
                                        <button name="action" value="reactivate" type="submit">Reactivate</button>
                                    <?php endif; ?>
                                </form>
                                <?php if (admin_is_administrator($adminUser)) : ?>
                                    <form method="post" onsubmit="return confirm('Delete this subscriber permanently?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
                                        <input type="hidden" name="id" value="<?php echo (int) $subscriber['id']; ?>">
                                        <button class="admin-row-delete-button" name="action" value="delete" type="submit">Delete Permanently</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$subscribers) : ?><tr><td colspan="4">No subscribers found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
