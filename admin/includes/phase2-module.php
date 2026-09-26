<?php
require_once __DIR__ . '/auth.php';

function phase2_config(string $key): array
{
    $configs = array(
        'events' => array(
            'title' => 'Events & Announcements', 'single' => 'Event', 'nav' => 'events', 'table' => 'events', 'title_field' => 'title', 'date_field' => 'start_date', 'upload' => 'featured_image', 'upload_folder' => 'events', 'statuses' => array('draft','pending_review','published','archived'),
            'filters' => array('type' => admin_event_types(), 'status' => array('draft','pending_review','published','archived')),
            'slug_source' => 'title',
            'fields' => array(
                array('name'=>'title','label'=>'Title','type'=>'text','required'=>true,'help'=>'The public URL slug is generated automatically from the title. Recommended maximum: 100-120 characters so public cards keep their approved size.'), array('name'=>'type','label'=>'Event Type','type'=>'select_other','options'=>admin_event_types(),'custom_label'=>'Custom Event Type','required'=>true),
                array('name'=>'short_description','label'=>'Short Description','type'=>'textarea','required'=>true,'help'=>'Recommended length: 160-250 characters. Longer text is shortened visually on public overview pages.'), array('name'=>'full_description','label'=>'Full Description','type'=>'textarea'),
                array('name'=>'start_date','label'=>'Start Date','type'=>'date','required'=>true), array('name'=>'end_date','label'=>'End Date','type'=>'date'), array('name'=>'start_time','label'=>'Start Time','type'=>'time'), array('name'=>'end_time','label'=>'End Time','type'=>'time'),
                array('name'=>'location','label'=>'Location','type'=>'select_other','options'=>admin_content_locations(),'custom_label'=>'Custom Location'), array('name'=>'featured_image','label'=>'Featured Image','type'=>'image'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Event Title','type'=>'Type','start_date'=>'Date','location'=>'Location','status'=>'Status')
        ),
        'programme-updates' => array(
            'title' => 'Programme Updates', 'single' => 'Programme Update', 'nav' => 'programme-updates', 'table' => 'programme_updates', 'title_field' => 'title', 'date_field' => 'update_date', 'upload' => 'featured_image', 'upload_folder' => 'programme-updates', 'statuses' => array('draft','pending_review','published','archived'),
            'filters' => array('programme' => admin_content_programmes(), 'status' => array('draft','pending_review','published','archived')),
            'slug_source' => 'title',
            'fields' => array(
                array('name'=>'programme','label'=>'Programme','type'=>'select_other','options'=>admin_content_programmes(),'custom_label'=>'Custom Programme / Category','required'=>true), array('name'=>'title','label'=>'Update Title','type'=>'text','required'=>true,'help'=>'The public URL slug is generated automatically from the title. Recommended maximum: 100-120 characters so public cards keep their approved size.'),
                array('name'=>'update_date','label'=>'Date','type'=>'date','required'=>true), array('name'=>'location','label'=>'Location','type'=>'select_other','options'=>admin_content_locations(),'custom_label'=>'Custom Location'), array('name'=>'short_description','label'=>'Short Description','type'=>'textarea','required'=>true,'help'=>'Recommended length: 160-250 characters. Longer text is shortened visually on public overview pages.'),
                array('name'=>'full_description','label'=>'Full Description','type'=>'textarea'), array('name'=>'featured_image','label'=>'Featured Image','type'=>'image'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Update Title','programme'=>'Programme','update_date'=>'Date','status'=>'Status')
        ),
        'impact' => array(
            'title' => 'Impact Updates', 'single' => 'Impact Update', 'nav' => 'impact', 'table' => 'impact_updates', 'title_field' => 'title', 'date_field' => 'impact_date', 'statuses' => array('draft','pending_review','published','archived'),
            'filters' => array('impact_type' => admin_impact_types(), 'status' => array('draft','pending_review','published','archived')),
            'fields' => array(
                array('name'=>'impact_type','label'=>'Impact Type','type'=>'select_other','options'=>admin_impact_types(),'custom_label'=>'Custom Impact Type','required'=>true), array('name'=>'title','label'=>'Impact Title','type'=>'text','required'=>true), array('name'=>'value','label'=>'Value','type'=>'text'), array('name'=>'unit','label'=>'Unit','type'=>'text'), array('name'=>'programme','label'=>'Programme','type'=>'select_other','options'=>admin_content_programmes(),'custom_label'=>'Custom Programme / Category'),
                array('name'=>'description','label'=>'Description','type'=>'textarea','required'=>true,'help'=>'Use plain content only. Public templates control layout, spacing, colors and card sizes.'), array('name'=>'impact_date','label'=>'Date','type'=>'date','required'=>true), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Impact Title','impact_type'=>'Impact Type','value'=>'Value','programme'=>'Programme','impact_date'=>'Date','status'=>'Status')
        ),
        'documents' => array(
            'title' => 'Documents / Resources', 'single' => 'Document', 'nav' => 'documents', 'table' => 'documents', 'title_field' => 'title', 'date_field' => 'published_at', 'document' => 'file_path', 'statuses' => array('draft','pending_review','published','archived'),
            'filters' => array('category' => admin_document_types(), 'status' => array('draft','pending_review','published','archived')),
            'fields' => array(
                array('name'=>'title','label'=>'Document Title','type'=>'text','required'=>true,'help'=>'Recommended maximum: 100-120 characters so public resource lists remain consistent.'), array('name'=>'description','label'=>'Description','type'=>'textarea'), array('name'=>'category','label'=>'Resource Type','type'=>'select_other','options'=>admin_document_types(),'custom_label'=>'Custom Resource Type'),
                array('name'=>'file_path','label'=>'File','type'=>'document'), array('name'=>'published_at','label'=>'Publication Date','type'=>'date'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Document Title','category'=>'Resource Type','file_type'=>'File Type','published_at'=>'Date','status'=>'Status')
        ),
        'partners' => array(
            'title' => 'Partners & Donors', 'single' => 'Partner / Donor', 'nav' => 'partners', 'table' => 'partners', 'title_field' => 'name', 'upload' => 'logo_path', 'upload_folder' => 'partners', 'statuses' => array('draft','pending_review','published','archived'),
            'filters' => array('type' => admin_partner_types(), 'status' => array('draft','pending_review','published','archived')),
            'fields' => array(
                array('name'=>'name','label'=>'Organization Name','type'=>'text','required'=>true,'help'=>'Use the organization name only. Logo slot sizing is controlled by the public template.'), array('name'=>'type','label'=>'Type','type'=>'select_other','options'=>admin_partner_types(),'custom_label'=>'Custom Organization Type','required'=>true), array('name'=>'logo_path','label'=>'Logo','type'=>'image'),
                array('name'=>'website_url','label'=>'Website URL','type'=>'url'), array('name'=>'description','label'=>'Short Description','type'=>'textarea'), array('name'=>'display_order','label'=>'Display Order','type'=>'number'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('logo_path'=>'Logo','name'=>'Organization Name','type'=>'Type','status'=>'Status')
        )
    );
    return $configs[$key];
}

function phase2_module_url(array $config, string $path = ''): string { return admin_url($config['nav'] . '/' . ltrim($path, '/')); }
function phase2_status_label(string $status): string { return admin_content_statuses()[$status] ?? ucfirst(str_replace('_', ' ', $status)); }
function phase2_status_class(string $status): string { return $status === 'published' || $status === 'active' ? 'published' : ($status === 'draft' || $status === 'hidden' || $status === 'pending_review' ? 'draft' : 'archived'); }
function phase2_publish_status(string $key): string { return 'published'; }
function phase2_draft_status(string $key): string { return 'draft'; }

function phase2_ensure_schema(string $key, PDO $pdo): void
{
    try {
        $config = phase2_config($key);
        $stmt = $pdo->query('SHOW COLUMNS FROM ' . $config['table'] . " LIKE 'status'");
        $column = $stmt ? $stmt->fetch() : null;
        $type = strtolower((string) ($column['Type'] ?? ''));
        if ($key === 'impact') {
            $columnCheck = $pdo->query("SHOW COLUMNS FROM impact_updates LIKE 'impact_type'");
            if ($columnCheck && !$columnCheck->fetch()) {
                $pdo->exec("ALTER TABLE impact_updates ADD impact_type VARCHAR(120) NOT NULL DEFAULT 'Beneficiaries Reached' AFTER title");
            }
        }
        if ($type !== '' && !str_contains($type, 'pending_review')) {
            if ($key === 'partners') {
                $pdo->exec("ALTER TABLE partners MODIFY status ENUM('draft','pending_review','published','archived','active','hidden') NOT NULL DEFAULT 'draft'");
                $pdo->exec("UPDATE partners SET status = 'published' WHERE status = 'active'");
                $pdo->exec("UPDATE partners SET status = 'draft' WHERE status = 'hidden'");
            } else {
                $pdo->exec('ALTER TABLE ' . $config['table'] . " MODIFY status ENUM('draft','pending_review','published','archived') NOT NULL DEFAULT 'draft'");
            }
        }
    } catch (Throwable $exception) {}
}

function phase2_clean_field_value(string $value, string $type): string
{
    $value = trim($value);
    if ($type === 'url') {
        $value = strip_tags($value);
        return preg_match('#^(https?://|mailto:|tel:|/)#i', $value) ? $value : '';
    }
    $value = preg_replace('#<(script|iframe|object|embed|style)\b[^>]*>.*?</\1>#is', '', $value) ?? '';
    $value = preg_replace('#</?(script|iframe|object|embed|style)\b[^>]*>#i', '', $value) ?? '';
    $text = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
    return trim(preg_replace('/\s+/', ' ', $text) ?? '');
}

function phase2_default_row(array $config): array
{
    $row = array('status' => $config['statuses'][0]);
    foreach ($config['fields'] as $field) { $row[$field['name']] = $field['type'] === 'number' ? '0' : ''; }
    if (isset($config['date_field'])) { $row[$config['date_field']] = date('Y-m-d'); }
    return $row;
}

function phase2_save(array $config, ?array $existing = null): array
{
    $pdo = admin_require_db();
    $errors = array();
    $row = $existing ?: phase2_default_row($config);
    foreach ($config['fields'] as $field) {
        if (in_array($field['type'], array('image','document','status'), true)) { continue; }
        if ($field['type'] === 'select_other') {
            $row[$field['name']] = admin_resolve_select_other_post($field['name'], $field['options'] ?? array());
            if (!empty($field['required'])) {
                $error = admin_select_other_required_error($field['name'], $field['label'], $field['options'] ?? array());
                if ($error !== '') { $errors[] = $error; }
            }
        } else {
            $row[$field['name']] = phase2_clean_field_value((string) ($_POST[$field['name']] ?? ''), $field['type']);
            if (!empty($field['required']) && $row[$field['name']] === '') { $errors[] = $field['label'] . ' is required.'; }
        }
    }
    $titleField = $config['title_field'];
    if (($row[$titleField] ?? '') === '') { $errors[] = $config['single'] . ' title is required.'; }
    if (isset($config['upload']) && !empty($_FILES[$config['upload']]['name'])) {
        try { $row[$config['upload']] = admin_store_upload($_FILES[$config['upload']], $config['upload_folder']); }
        catch (Throwable $e) { $errors[] = $e->getMessage(); }
    }
    if (isset($config['document']) && !empty($_FILES[$config['document']]['name'])) {
        try { $row[$config['document']] = admin_store_document_upload($_FILES[$config['document']]); $row['file_type'] = strtoupper(pathinfo($row[$config['document']], PATHINFO_EXTENSION)); }
        catch (Throwable $e) { $errors[] = $e->getMessage(); }
    } elseif (isset($config['document']) && !$existing && empty($row[$config['document']])) {
        $errors[] = 'File is required.';
    }
    $action = (string) ($_POST['submit_action'] ?? 'draft');
    $row['status'] = admin_content_status_for_action($action, $existing ?: $row);
    if (!admin_can_transition_content($action, $existing ?: $row)) {
        $errors[] = 'You do not have permission to perform that workflow action.';
    }
    if ($errors) { return array($row, $errors, null); }

    if (isset($config['slug_source'])) { $row['slug'] = admin_slug_for_save($pdo, $config['table'], (string) ($row[$config['slug_source']] ?? $row[$titleField]), $existing); }
    if (isset($config['date_field']) && empty($row[$config['date_field']])) { $row[$config['date_field']] = date('Y-m-d'); }
    $fields = array();
    foreach ($config['fields'] as $field) {
        if ($field['type'] === 'image' && empty($row[$field['name']])) { continue; }
        if ($field['type'] === 'document' && empty($row[$field['name']])) { continue; }
        $fields[] = $field['name'];
    }
    if (isset($row['slug']) && !in_array('slug', $fields, true)) { $fields[] = 'slug'; }
    if (isset($row['file_type']) && !in_array('file_type', $fields, true)) { $fields[] = 'file_type'; }
    if ($existing) {
        $sets = array_map(static fn($f) => $f . ' = ?', $fields);
        $params = array_map(static fn($f) => $row[$f] ?? null, $fields);
        $params[] = (int) $existing['id'];
        $pdo->prepare('UPDATE ' . $config['table'] . ' SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
        $id = (int) $existing['id'];
        admin_log($row['status'] === 'published' ? 'published' : ($row['status'] === 'pending_review' ? 'submitted_for_review' : 'edited'), $config['table'], $id, $config['single'] . ' saved: ' . $row[$titleField]);
    } else {
        $fields[] = 'created_by';
        $params = array_map(static fn($f) => $f === 'created_by' ? ($_SESSION['admin_user_id'] ?? null) : ($row[$f] ?? null), $fields);
        $pdo->prepare('INSERT INTO ' . $config['table'] . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_fill(0, count($fields), '?')) . ')')->execute($params);
        $id = (int) $pdo->lastInsertId();
        admin_log($row['status'] === 'pending_review' ? 'submitted_for_review' : ($row['status'] === 'published' ? 'published' : 'created'), $config['table'], $id, $config['single'] . ' created: ' . $row[$titleField]);
    }
    return array($row, array(), $id);
}

function phase2_render_form(array $config, array $row, array $errors, string $action): void
{
    foreach ($errors as $error) { echo '<div class="admin-alert admin-alert--error" style="margin:0 0 12px">' . e($error) . '</div>'; }
    echo '<form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '"><div class="admin-form-grid">';
    foreach ($config['fields'] as $field) {
        $name = $field['name']; $value = (string) ($row[$name] ?? '');
        if ($field['type'] === 'select_other') { admin_render_select_other($name, $field['label'], $value, $field['options'] ?? array(), $field['custom_label'] ?? ('Custom ' . $field['label']), !empty($field['required'])); continue; }
        echo '<div class="admin-field"><label>' . e($field['label']) . '</label>';
        if ($field['type'] === 'textarea') { echo '<textarea class="admin-textarea" name="' . e($name) . '">' . e($value) . '</textarea>'; if (!empty($field['help'])) { echo '<small>' . e($field['help']) . '</small>'; } }
        elseif ($field['type'] === 'select') { echo '<select class="admin-select" name="' . e($name) . '">'; foreach (($field['options'] ?? array()) as $opt) { echo '<option value="' . e($opt) . '" ' . ($value === (string)$opt ? 'selected' : '') . '>' . e($opt === '' ? 'None' : $opt) . '</option>'; } echo '</select>'; }
        elseif ($field['type'] === 'status') { echo '<input type="hidden" name="current_status" value="' . e($value) . '"><p class="admin-muted">Current status: ' . e(phase2_status_label($value ?: 'draft')) . '</p>'; }
        elseif ($field['type'] === 'image') { if ($value) { echo '<div class="admin-image-preview"><img src="' . site_url($value) . '" alt="Current image"></div>'; } echo '<input class="admin-input" type="file" name="' . e($name) . '" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">'; }
        elseif ($field['type'] === 'document') { if ($value) { echo '<p><a class="admin-button admin-button--light" href="' . site_url($value) . '" target="_blank">View Current File</a></p>'; } echo '<input class="admin-input" type="file" name="' . e($name) . '" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">'; }
        else { echo '<input class="admin-input" type="' . e($field['type']) . '" name="' . e($name) . '" value="' . e($value) . '">'; if (!empty($field['help'])) { echo '<small>' . e($field['help']) . '</small>'; } }
        echo '</div>';
    }
    echo '</div><div class="admin-actions"><a class="admin-button admin-button--yellow" href="' . phase2_module_url($config) . '">Cancel</a>'; foreach (admin_content_allowed_actions($row) as $workflowAction) { $class = $workflowAction === 'publish' ? 'admin-button' : 'admin-button admin-button--light'; echo '<button class="' . e($class) . '" type="submit" name="submit_action" value="' . e($workflowAction) . '">' . e(admin_content_action_label($workflowAction)) . '</button>'; } echo '</div></form>';
}

function phase2_run_list(string $key): void
{
    admin_require_permission('content.manage');
    if (!$edit && !admin_can('content.create')) { admin_forbidden(); }
    $config = phase2_config($key); $pdo = admin_require_db(); phase2_ensure_schema($key, $pdo);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        admin_require_csrf();
        $id = (int) ($_POST['id'] ?? 0); $action = (string) ($_POST['action'] ?? '');
        if ($id > 0) {
            if ($action === 'delete') {
                if (!admin_can('content.delete')) { admin_forbidden(); }
                $stmt = $pdo->prepare('SELECT ' . $config['title_field'] . ' FROM ' . $config['table'] . ' WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $title = (string) $stmt->fetchColumn();
                if ($title !== '') { $pdo->prepare('DELETE FROM ' . $config['table'] . ' WHERE id = ?')->execute([$id]); admin_log('deleted', $config['table'], $id, $config['single'] . ' deleted permanently: ' . $title); admin_flash('success', $config['single'] . ' deleted successfully.'); }
            } else {
                $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $targetRow = $stmt->fetch();
                $workflowAction = $action === 'pending_review' ? 'submit_review' : $action;
                $status = in_array($workflowAction, array('publish','unpublish','archive','return_draft','submit_review'), true) ? admin_content_status_for_action($workflowAction, $targetRow ?: array()) : null;
                if ($status) { if (!admin_can_transition_content($workflowAction, $targetRow ?: array())) { admin_forbidden(); } $pdo->prepare('UPDATE ' . $config['table'] . ' SET status = ? WHERE id = ?')->execute([$status, $id]); admin_log($workflowAction, $config['table'], $id, $config['single'] . ' status changed.'); admin_flash('success', $config['single'] . ' updated.'); }
            }
        }
        header('Location: ' . phase2_module_url($config)); exit;
    }
    $search = trim((string) ($_GET['search'] ?? '')); $status = trim((string) ($_GET['status'] ?? '')); $filterName = array_key_first($config['filters']); $filterValue = trim((string) ($_GET[$filterName] ?? ''));
    $where = array('1=1'); $params = array();
    if ($search !== '') { $where[] = $config['title_field'] . ' LIKE ?'; $params[] = '%' . $search . '%'; }
    if ($status !== '') { $where[] = 'status = ?'; $params[] = $status; }
    if ($filterValue !== '') { $where[] = $filterName . ' = ?'; $params[] = $filterValue; }
    $order = isset($config['date_field']) ? $config['date_field'] . ' DESC, id DESC' : 'display_order ASC, id DESC';
    $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order); $stmt->execute($params); $rows = $stmt->fetchAll();
    $adminTitle = $config['title']; $activeNav = $config['nav']; require __DIR__ . '/admin-header.php';
    echo '<div class="admin-toolbar"><form class="admin-filters" method="get"><input class="admin-input" style="width:220px" name="search" placeholder="Search" value="' . e($search) . '">';
    foreach ($config['filters'] as $fname => $opts) { if ($fname === 'status') { continue; } echo '<select class="admin-select" style="width:190px" name="' . e($fname) . '"><option value="">All ' . e(str_replace('_',' ', $fname)) . '</option>'; foreach ($opts as $opt) { echo '<option value="' . e($opt) . '" ' . ($filterValue === $opt ? 'selected' : '') . '>' . e($opt) . '</option>'; } echo '</select>'; break; }
    echo '<select class="admin-select" style="width:160px" name="status"><option value="">All statuses</option>'; foreach ($config['statuses'] as $s) { echo '<option value="' . e($s) . '" ' . ($status === $s ? 'selected' : '') . '>' . e(phase2_status_label($s)) . '</option>'; } echo '</select><button class="admin-button admin-button--light" type="submit">Filter</button></form><a class="admin-button" href="' . phase2_module_url($config, 'add.php') . '"><i class="bi bi-plus-lg"></i> Add ' . e($config['single']) . '</a></div>';
    echo '<section class="admin-table-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr>'; foreach ($config['columns'] as $label) { echo '<th>' . e($label) . '</th>'; } echo '<th>Actions</th></tr></thead><tbody>';
    foreach ($rows as $row) { echo '<tr>'; foreach ($config['columns'] as $field => $label) { echo '<td>'; if (str_contains($field, 'logo') || str_contains($field, 'image')) { if (!empty($row[$field])) echo '<img class="admin-thumb" src="' . site_url($row[$field]) . '" alt="">'; } elseif ($field === 'status') { echo '<span class="admin-status admin-status--' . e(phase2_status_class($row[$field])) . '">' . e(phase2_status_label($row[$field])) . '</span>'; } elseif (str_contains($field, 'date') || $field === 'published_at') { echo e(ssvdp_format_date($row[$field], '')); } else { echo e((string) ($row[$field] ?? '')); } echo '</td>'; }
        $isVisible = in_array($row['status'], array('published','active'), true); echo '<td><div class="admin-row-actions"><a href="' . phase2_module_url($config, 'edit.php?id=' . (int)$row['id']) . '">Edit</a><a href="' . phase2_module_url($config, 'preview.php?id=' . (int)$row['id']) . '" target="_blank">Preview</a><form method="post"><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><input type="hidden" name="action" value="' . ($isVisible ? 'unpublish' : 'publish') . '"><button type="submit">' . ($isVisible ? 'Unpublish' : 'Publish') . '</button></form><form method="post" onsubmit="return confirm(\'Archive this item? It will no longer appear publicly.\');"><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><input type="hidden" name="action" value="archive"><button type="submit">Archive</button></form>'; if (admin_is_administrator()) { echo '<button class="admin-row-delete-button" type="button" data-phase2-delete-trigger data-phase2-id="' . (int)$row['id'] . '" data-phase2-title="' . e((string) $row[$config['title_field']]) . '">Delete</button>'; } echo '</div></td></tr>'; }
    if (!$rows) { echo '<tr><td colspan="' . (count($config['columns']) + 1) . '">No items found.</td></tr>'; }
    echo '</tbody></table></div></section><dialog class="admin-delete-dialog" data-phase2-delete-dialog><form method="post" data-phase2-delete-form><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value=""><h2>Delete ' . e($config['single']) . '</h2><p>Are you sure you want to delete this item?</p><p><strong data-phase2-delete-title></strong></p><div class="admin-delete-dialog__actions"><button class="admin-button admin-button--light" type="button" data-phase2-delete-cancel>Cancel</button><button class="admin-button admin-button--danger" type="submit">Delete Permanently</button></div></form></dialog>';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var d=document.querySelector("[data-phase2-delete-dialog]");if(!d){return;}var f=d.querySelector("[data-phase2-delete-form]"),i=f.querySelector("input[name=id]"),t=d.querySelector("[data-phase2-delete-title]"),c=d.querySelector("[data-phase2-delete-cancel]");document.querySelectorAll("[data-phase2-delete-trigger]").forEach(function(b){b.addEventListener("click",function(){i.value=b.getAttribute("data-phase2-id")||"";t.textContent=b.getAttribute("data-phase2-title")||"";if(typeof d.showModal==="function"){d.showModal();}else if(window.confirm("Are you sure you want to delete this item?\\n\\n"+t.textContent)){f.submit();}});});c.addEventListener("click",function(){d.close();});});</script>';
    require __DIR__ . '/admin-footer.php';
}

function phase2_run_form(string $key, bool $edit): void
{
    admin_require_permission('content.manage');
    if (!$edit && !admin_can('content.create')) { admin_forbidden(); }
    $config = phase2_config($key); $pdo = admin_require_db(); phase2_ensure_schema($key, $pdo); $row = phase2_default_row($config); $errors = array(); $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    if ($edit) { $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id = ? LIMIT 1'); $stmt->execute([$id]); $row = $stmt->fetch(); if (!$row) { admin_flash('error', $config['single'] . ' not found.'); header('Location: ' . phase2_module_url($config)); exit; } if (!admin_can_edit_content($row)) { admin_forbidden(); } }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { admin_require_csrf(); [$row, $errors, $savedId] = phase2_save($config, $edit ? $row : null); if (!$errors) { admin_flash('success', $config['single'] . ' saved.'); header('Location: ' . phase2_module_url($config)); exit; } }
    $adminTitle = ($edit ? 'Edit ' : 'Add ') . $config['single']; $activeNav = $config['nav']; require __DIR__ . '/admin-header.php'; echo '<section class="admin-panel">'; phase2_render_form($config, $row, $errors, $edit ? 'edit' : 'add'); echo '</section>'; require __DIR__ . '/admin-footer.php';
}

function phase2_run_preview(string $key): void
{
    $config = phase2_config($key); admin_require_auth(); $pdo = admin_require_db(); phase2_ensure_schema($key, $pdo); $id = (int) ($_GET['id'] ?? 0); $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id = ? LIMIT 1'); $stmt->execute([$id]); $row = $stmt->fetch(); if (!$row) exit('Item not found.');
    ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Preview: <?php echo e($row[$config['title_field']]); ?></title><link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>"></head><body class="admin-body"><main class="admin-content"><div class="admin-alert">Preview only. Visible to signed-in administrators and does not publish changes.</div><section class="admin-panel"><h1><?php echo e($row[$config['title_field']]); ?></h1><?php foreach ($config['fields'] as $field) : if (in_array($field['type'], array('image','document'), true)) continue; ?><p><strong><?php echo e($field['label']); ?>:</strong><br><?php echo nl2br(e((string)($row[$field['name']] ?? ''))); ?></p><?php endforeach; ?><?php if (!empty($config['upload']) && !empty($row[$config['upload']])) : ?><div class="admin-image-preview"><img src="<?php echo site_url($row[$config['upload']]); ?>" alt=""></div><?php endif; ?><?php if (!empty($config['document']) && !empty($row[$config['document']])) : ?><p><a class="admin-button" href="<?php echo site_url($row[$config['document']]); ?>" target="_blank">Open Document</a></p><?php endif; ?></section></main></body></html><?php
}
