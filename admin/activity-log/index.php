<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_role('administrator');
$pdo = admin_require_db();
$adminTitle = 'Activity Log';
$activeNav = 'activity-log';
$search = trim((string) ($_GET['search'] ?? ''));
$userId = (int) ($_GET['user_id'] ?? 0);
$module = trim((string) ($_GET['module'] ?? ''));
$action = trim((string) ($_GET['action'] ?? ''));
$dateFrom = trim((string) ($_GET['date_from'] ?? ''));
$dateTo = trim((string) ($_GET['date_to'] ?? ''));
$params = array();
$where = 'WHERE 1=1';
if ($search !== '') { $where .= ' AND (l.description LIKE ? OR l.entity_type LIKE ? OR l.action LIKE ? OR u.name LIKE ?)'; $like = '%' . $search . '%'; array_push($params, $like, $like, $like, $like); }
if ($userId > 0) { $where .= ' AND l.user_id = ?'; $params[] = $userId; }
if ($module !== '') { $where .= ' AND l.entity_type = ?'; $params[] = $module; }
if ($action !== '') { $where .= ' AND l.action = ?'; $params[] = $action; }
if ($dateFrom !== '') { $where .= ' AND DATE(l.created_at) >= ?'; $params[] = $dateFrom; }
if ($dateTo !== '') { $where .= ' AND DATE(l.created_at) <= ?'; $params[] = $dateTo; }
$stmt = $pdo->prepare("SELECT l.*, u.name AS user_name FROM admin_activity_log l LEFT JOIN admin_users u ON u.id = l.user_id {$where} ORDER BY l.created_at DESC LIMIT 300");
$stmt->execute($params);
$logs = $stmt->fetchAll();
$users = $pdo->query('SELECT id, name FROM admin_users ORDER BY name ASC')->fetchAll();
$modules = $pdo->query('SELECT DISTINCT entity_type FROM admin_activity_log ORDER BY entity_type ASC')->fetchAll(PDO::FETCH_COLUMN);
$actions = $pdo->query('SELECT DISTINCT action FROM admin_activity_log ORDER BY action ASC')->fetchAll(PDO::FETCH_COLUMN);
require __DIR__ . '/../includes/admin-header.php';
?>
<section class="admin-table-card"><div class="admin-toolbar"><div><h2>Activity Log</h2><p class="admin-muted">Security and content-management actions.</p></div></div><form class="admin-filters" method="get" style="margin-bottom:18px"><input class="admin-input" style="width:190px" name="search" placeholder="Search" value="<?php echo e($search); ?>"><select class="admin-select" style="width:170px" name="user_id"><option value="0">All users</option><?php foreach ($users as $u) : ?><option value="<?php echo (int) $u['id']; ?>" <?php echo $userId === (int) $u['id'] ? 'selected' : ''; ?>><?php echo e($u['name']); ?></option><?php endforeach; ?></select><select class="admin-select" style="width:170px" name="module"><option value="">All modules</option><?php foreach ($modules as $m) : ?><option value="<?php echo e($m); ?>" <?php echo $module === $m ? 'selected' : ''; ?>><?php echo e($m); ?></option><?php endforeach; ?></select><select class="admin-select" style="width:150px" name="action"><option value="">All actions</option><?php foreach ($actions as $a) : ?><option value="<?php echo e($a); ?>" <?php echo $action === $a ? 'selected' : ''; ?>><?php echo e($a); ?></option><?php endforeach; ?></select><input class="admin-input" style="width:145px" type="date" name="date_from" value="<?php echo e($dateFrom); ?>"><input class="admin-input" style="width:145px" type="date" name="date_to" value="<?php echo e($dateTo); ?>"><button class="admin-button admin-button--light" type="submit">Filter</button></form><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Date/Time</th><th>Staff User</th><th>Action</th><th>Module</th><th>Description</th><th>IP Address</th></tr></thead><tbody><?php foreach ($logs as $log) : ?><tr><td><?php echo e(admin_phase3_date($log['created_at'])); ?></td><td><?php echo e($log['user_name'] ?: 'System'); ?></td><td><?php echo e($log['action']); ?></td><td><?php echo e($log['entity_type']); ?></td><td><?php echo e($log['description']); ?></td><td><?php echo e($log['ip_address'] ?? ''); ?></td></tr><?php endforeach; ?><?php if (!$logs) : ?><tr><td colspan="6">No activity found.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>