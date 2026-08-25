<?php
require_once __DIR__ . '/auth.php';

function phase2_config(string $key): array
{
    $programmes = admin_get_programme_names();
    $configs = array(
        'events' => array(
            'title' => 'Events & Announcements', 'single' => 'Event', 'nav' => 'events', 'table' => 'events', 'title_field' => 'title', 'date_field' => 'start_date', 'upload' => 'featured_image', 'upload_folder' => 'events', 'statuses' => array('draft','published','archived'),
            'filters' => array('type' => array('Announcement','Event','Training','Meeting'), 'status' => array('draft','published','archived')),
            'fields' => array(
                array('name'=>'title','label'=>'Title','type'=>'text','required'=>true,'help'=>'Recommended maximum: 100-120 characters so public cards keep their approved size.'), array('name'=>'slug','label'=>'Slug','type'=>'text'), array('name'=>'type','label'=>'Type','type'=>'select','options'=>array('Announcement','Event','Training','Meeting')),
                array('name'=>'short_description','label'=>'Short Description','type'=>'textarea','required'=>true,'help'=>'Recommended length: 160-250 characters. Longer text is shortened visually on public overview pages.'), array('name'=>'full_description','label'=>'Full Description','type'=>'textarea'),
                array('name'=>'start_date','label'=>'Start Date','type'=>'date','required'=>true), array('name'=>'end_date','label'=>'End Date','type'=>'date'), array('name'=>'start_time','label'=>'Start Time','type'=>'time'), array('name'=>'end_time','label'=>'End Time','type'=>'time'),
                array('name'=>'location','label'=>'Location','type'=>'text'), array('name'=>'featured_image','label'=>'Featured Image','type'=>'image'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Event Title','type'=>'Type','start_date'=>'Date','location'=>'Location','status'=>'Status')
        ),
        'programme-updates' => array(
            'title' => 'Programme Updates', 'single' => 'Programme Update', 'nav' => 'programme-updates', 'table' => 'programme_updates', 'title_field' => 'title', 'date_field' => 'update_date', 'upload' => 'featured_image', 'upload_folder' => 'programme-updates', 'statuses' => array('draft','published','archived'),
            'filters' => array('programme' => $programmes, 'status' => array('draft','published','archived')),
            'fields' => array(
                array('name'=>'programme','label'=>'Programme','type'=>'select','options'=>$programmes,'required'=>true), array('name'=>'title','label'=>'Update Title','type'=>'text','required'=>true,'help'=>'Recommended maximum: 100-120 characters so public cards keep their approved size.'), array('name'=>'slug','label'=>'Slug','type'=>'text'),
                array('name'=>'update_date','label'=>'Date','type'=>'date','required'=>true), array('name'=>'location','label'=>'Location','type'=>'text'), array('name'=>'short_description','label'=>'Short Description','type'=>'textarea','required'=>true,'help'=>'Recommended length: 160-250 characters. Longer text is shortened visually on public overview pages.'),
                array('name'=>'full_description','label'=>'Full Description','type'=>'textarea'), array('name'=>'featured_image','label'=>'Featured Image','type'=>'image'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Update Title','programme'=>'Programme','update_date'=>'Date','status'=>'Status')
        ),
        'impact' => array(
            'title' => 'Impact Updates', 'single' => 'Impact Update', 'nav' => 'impact', 'table' => 'impact_updates', 'title_field' => 'title', 'date_field' => 'impact_date', 'statuses' => array('draft','published','archived'),
            'filters' => array('programme' => $programmes, 'status' => array('draft','published','archived')),
            'fields' => array(
                array('name'=>'title','label'=>'Impact Title','type'=>'text','required'=>true), array('name'=>'value','label'=>'Value','type'=>'text'), array('name'=>'unit','label'=>'Unit','type'=>'text'), array('name'=>'programme','label'=>'Programme','type'=>'select','options'=>array_merge(array(''),$programmes)),
                array('name'=>'description','label'=>'Description','type'=>'textarea','required'=>true,'help'=>'Use plain content only. Public templates control layout, spacing, colors and card sizes.'), array('name'=>'impact_date','label'=>'Date','type'=>'date','required'=>true), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Impact Title','value'=>'Value','programme'=>'Programme','impact_date'=>'Date','status'=>'Status')
        ),
        'documents' => array(
            'title' => 'Documents / Resources', 'single' => 'Document', 'nav' => 'documents', 'table' => 'documents', 'title_field' => 'title', 'date_field' => 'published_at', 'document' => 'file_path', 'statuses' => array('draft','published','archived'),
            'filters' => array('category' => array('Reports','Policies','Forms','Resources'), 'status' => array('draft','published','archived')),
            'fields' => array(
                array('name'=>'title','label'=>'Document Title','type'=>'text','required'=>true,'help'=>'Recommended maximum: 100-120 characters so public resource lists remain consistent.'), array('name'=>'description','label'=>'Description','type'=>'textarea'), array('name'=>'category','label'=>'Category','type'=>'select','options'=>array('Reports','Policies','Forms','Resources')),
                array('name'=>'file_path','label'=>'File','type'=>'document'), array('name'=>'published_at','label'=>'Publication Date','type'=>'date'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('title'=>'Document Title','category'=>'Category','file_type'=>'File Type','published_at'=>'Date','status'=>'Status')
        ),
        'partners' => array(
            'title' => 'Partners & Donors', 'single' => 'Partner / Donor', 'nav' => 'partners', 'table' => 'partners', 'title_field' => 'name', 'upload' => 'logo_path', 'upload_folder' => 'partners', 'statuses' => array('active','hidden','archived'),
            'filters' => array('type' => array('Partner','Donor'), 'status' => array('active','hidden','archived')),
            'fields' => array(
                array('name'=>'name','label'=>'Organization Name','type'=>'text','required'=>true,'help'=>'Use the organization name only. Logo slot sizing is controlled by the public template.'), array('name'=>'type','label'=>'Type','type'=>'select','options'=>array('Partner','Donor')), array('name'=>'logo_path','label'=>'Logo','type'=>'image'),
                array('name'=>'website_url','label'=>'Website URL','type'=>'url'), array('name'=>'description','label'=>'Short Description','type'=>'textarea'), array('name'=>'display_order','label'=>'Display Order','type'=>'number'), array('name'=>'status','label'=>'Status','type'=>'status')
            ),
            'columns' => array('logo_path'=>'Logo','name'=>'Organization Name','type'=>'Type','display_order'=>'Display Order','status'=>'Status')
        )
    );
    return $configs[$key];
}

function phase2_module_url(array $config, string $path = ''): string { return admin_url($config['nav'] . '/' . ltrim($path, '/')); }
function phase2_status_label(string $status): string { return ucfirst($status); }
function phase2_status_class(string $status): string { return in_array($status, array('published','active'), true) ? 'active' : ($status === 'draft' ? 'draft' : ''); }

function phase2_clean_field_value(string $value, string $type): string
{
    $value = trim($value);
    if ($type === 'url') {
        $value = strip_tags($value);
        return preg_match('#^(https?://|mailto:|tel:|/)#i', $value) ? $value : '';
    }

    $value = preg_replace('#<(script|iframe|object|embed|style)\b[^>]*>.*?</\1>#is', '', $value) ?? '';
    $value = preg_replace('#</?(script|iframe|object|embed|style)\b[^>]*>#i', '', $value) ?? '';
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
    return trim(preg_replace('/\s+/', ' ', $value) ?? '');
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
        if (in_array($field['type'], array('image','document'), true)) { continue; }
        $row[$field['name']] = phase2_clean_field_value((string) ($_POST[$field['name']] ?? ''), $field['type']);
        if (!empty($field['required']) && $row[$field['name']] === '') { $errors[] = $field['label'] . ' is required.'; }
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
    if ($errors) { return array($row, $errors, null); }

    if (array_key_exists('slug', $row)) { $row['slug'] = admin_slug_unique($pdo, $config['table'], $row['slug'] !== '' ? $row['slug'] : $row[$titleField], $existing['id'] ?? null); }
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
        admin_log('edited', $config['table'], $id, $config['single'] . ' edited: ' . $row[$titleField]);
    } else {
        $fields[] = 'created_by';
        $params = array_map(static fn($f) => $f === 'created_by' ? ($_SESSION['admin_user_id'] ?? null) : ($row[$f] ?? null), $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $pdo->prepare('INSERT INTO ' . $config['table'] . ' (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')')->execute($params);
        $id = (int) $pdo->lastInsertId();
        admin_log('created', $config['table'], $id, $config['single'] . ' created: ' . $row[$titleField]);
    }
    return array($row, array(), $id);
}

function phase2_render_form(array $config, array $row, array $errors, string $action): void
{
    foreach ($errors as $error) { echo '<div class="admin-alert admin-alert--error" style="margin:0 0 12px">' . e($error) . '</div>'; }
    echo '<form class="admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '">';
    echo '<div class="admin-form-grid">';
    foreach ($config['fields'] as $field) {
        $name = $field['name']; $value = (string) ($row[$name] ?? '');
        echo '<div class="admin-field"><label>' . e($field['label']) . '</label>';
        if ($field['type'] === 'textarea') { echo '<textarea class="admin-textarea" name="' . e($name) . '">' . e($value) . '</textarea>'; if (!empty($field['help'])) { echo '<small>' . e($field['help']) . '</small>'; } }
        elseif ($field['type'] === 'select') { echo '<select class="admin-select" name="' . e($name) . '">'; foreach (($field['options'] ?? array()) as $opt) { echo '<option value="' . e($opt) . '" ' . ($value === (string)$opt ? 'selected' : '') . '>' . e($opt === '' ? 'None' : $opt) . '</option>'; } echo '</select>'; }
        elseif ($field['type'] === 'status') { echo '<select class="admin-select" name="status">'; foreach ($config['statuses'] as $status) { echo '<option value="' . e($status) . '" ' . ($value === $status ? 'selected' : '') . '>' . e(phase2_status_label($status)) . '</option>'; } echo '</select>'; }
        elseif ($field['type'] === 'image') { if ($value) { echo '<div class="admin-image-preview"><img src="' . site_url($value) . '" alt="Current image"></div>'; } echo '<input class="admin-input" type="file" name="' . e($name) . '" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">'; }
        elseif ($field['type'] === 'document') { if ($value) { echo '<p><a class="admin-button admin-button--light" href="' . site_url($value) . '" target="_blank">View Current File</a></p>'; } echo '<input class="admin-input" type="file" name="' . e($name) . '" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">'; }
        else { echo '<input class="admin-input" type="' . e($field['type']) . '" name="' . e($name) . '" value="' . e($value) . '">'; if (!empty($field['help'])) { echo '<small>' . e($field['help']) . '</small>'; } }
        echo '</div>';
    }
    echo '</div><div class="admin-actions"><button class="admin-button admin-button--light" type="submit" name="save_mode" value="draft">Save</button><a class="admin-button admin-button--yellow" href="' . phase2_module_url($config) . '">Cancel</a><button class="admin-button" type="submit" name="save_mode" value="publish">Save & Continue</button></div></form>';
}

function phase2_run_list(string $key): void
{
    $config = phase2_config($key); $pdo = admin_require_db(); admin_require_csrf();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int) ($_POST['id'] ?? 0); $action = (string) ($_POST['action'] ?? '');
        if ($id > 0) {
            $status = null;
            if ($action === 'publish') { $status = $key === 'partners' ? 'active' : 'published'; }
            if ($action === 'unpublish') { $status = $key === 'partners' ? 'hidden' : 'draft'; }
            if ($action === 'archive') { $status = 'archived'; }
            if ($status) { $pdo->prepare('UPDATE ' . $config['table'] . ' SET status = ? WHERE id = ?')->execute([$status, $id]); admin_log($action, $config['table'], $id, $config['single'] . ' status changed.'); admin_flash('success', $config['single'] . ' updated.'); }
        }
        header('Location: ' . phase2_module_url($config)); exit;
    }
    $search = trim((string) ($_GET['search'] ?? '')); $status = trim((string) ($_GET['status'] ?? '')); $filterName = array_key_first($config['filters']); $filterValue = trim((string) ($_GET[$filterName] ?? ''));
    $where = array('status <> ?'); $params = array('archived');
    if ($search !== '') { $where[] = $config['title_field'] . ' LIKE ?'; $params[] = '%' . $search . '%'; }
    if ($status !== '') { $where[] = 'status = ?'; $params[] = $status; }
    if ($filterValue !== '') { $where[] = $filterName . ' = ?'; $params[] = $filterValue; }
    $order = isset($config['date_field']) ? $config['date_field'] . ' DESC, id DESC' : 'display_order ASC, id DESC';
    $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order); $stmt->execute($params); $rows = $stmt->fetchAll();
    $adminTitle = $config['title']; $activeNav = $config['nav']; require __DIR__ . '/admin-header.php';
    echo '<div class="admin-toolbar"><form class="admin-filters" method="get"><input class="admin-input" style="width:220px" name="search" placeholder="Search" value="' . e($search) . '">';
    foreach ($config['filters'] as $fname => $opts) { echo '<select class="admin-select" style="width:190px" name="' . e($fname) . '"><option value="">All ' . e(str_replace('_',' ', $fname)) . '</option>'; foreach ($opts as $opt) { echo '<option value="' . e($opt) . '" ' . ($filterValue === $opt ? 'selected' : '') . '>' . e($opt) . '</option>'; } echo '</select>'; break; }
    echo '<select class="admin-select" style="width:150px" name="status"><option value="">All statuses</option>'; foreach ($config['statuses'] as $s) { if ($s === 'archived') continue; echo '<option value="' . e($s) . '" ' . ($status === $s ? 'selected' : '') . '>' . e(phase2_status_label($s)) . '</option>'; } echo '</select><button class="admin-button admin-button--light" type="submit">Filter</button></form><a class="admin-button" href="' . phase2_module_url($config, 'add.php') . '"><i class="bi bi-plus-lg"></i> Add ' . e($config['single']) . '</a></div>';
    echo '<section class="admin-table-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr>'; foreach ($config['columns'] as $label) { echo '<th>' . e($label) . '</th>'; } echo '<th>Actions</th></tr></thead><tbody>';
    foreach ($rows as $row) { echo '<tr>'; foreach ($config['columns'] as $field => $label) { echo '<td>'; if (str_contains($field, 'logo') || str_contains($field, 'image')) { if (!empty($row[$field])) echo '<img class="admin-thumb" src="' . site_url($row[$field]) . '" alt="">'; } elseif ($field === 'status') { echo '<span class="admin-status admin-status--' . e(phase2_status_class($row[$field])) . '">' . e(phase2_status_label($row[$field])) . '</span>'; } elseif (str_contains($field, 'date') || $field === 'published_at') { echo e(ssvdp_format_date($row[$field], '')); } else { echo e((string) ($row[$field] ?? '')); } echo '</td>'; }
        $isVisible = in_array($row['status'], array('published','active'), true); echo '<td><div class="admin-row-actions"><a href="' . phase2_module_url($config, 'edit.php?id=' . (int)$row['id']) . '">Edit</a><a href="' . phase2_module_url($config, 'preview.php?id=' . (int)$row['id']) . '" target="_blank">Preview</a><form method="post"><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><input type="hidden" name="action" value="' . ($isVisible ? 'unpublish' : 'publish') . '"><button type="submit">' . ($isVisible ? ($key === 'partners' ? 'Hide' : 'Unpublish') : ($key === 'partners' ? 'Activate' : 'Publish')) . '</button></form><form method="post" onsubmit="return confirm(\'Archive this item?\');"><input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><input type="hidden" name="action" value="archive"><button type="submit">Archive</button></form></div></td></tr>'; }
    if (!$rows) { echo '<tr><td colspan="' . (count($config['columns']) + 1) . '">No items found.</td></tr>'; }
    echo '</tbody></table></div></section>'; require __DIR__ . '/admin-footer.php';
}

function phase2_run_form(string $key, bool $edit): void
{
    $config = phase2_config($key); $pdo = admin_require_db(); $row = phase2_default_row($config); $errors = array(); $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    if ($edit) { $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id = ? LIMIT 1'); $stmt->execute([$id]); $row = $stmt->fetch(); if (!$row) { admin_flash('error', $config['single'] . ' not found.'); header('Location: ' . phase2_module_url($config)); exit; } }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { admin_require_csrf(); [$row, $errors, $savedId] = phase2_save($config, $edit ? $row : null); if (!$errors) { admin_flash('success', $config['single'] . ' saved.'); header('Location: ' . phase2_module_url($config)); exit; } }
    $adminTitle = ($edit ? 'Edit ' : 'Add ') . $config['single']; $activeNav = $config['nav']; require __DIR__ . '/admin-header.php'; echo '<section class="admin-panel">'; phase2_render_form($config, $row, $errors, $edit ? 'edit' : 'add'); echo '</section>'; require __DIR__ . '/admin-footer.php';
}

function phase2_run_preview(string $key): void
{
    $config = phase2_config($key); admin_require_auth(); $pdo = admin_require_db(); $id = (int) ($_GET['id'] ?? 0); $stmt = $pdo->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id = ? LIMIT 1'); $stmt->execute([$id]); $row = $stmt->fetch(); if (!$row) exit('Item not found.');
    ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Preview: <?php echo e($row[$config['title_field']]); ?></title><link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>"></head><body class="admin-body"><main class="admin-content"><div class="admin-alert">Preview only. Visible to signed-in administrators.</div><section class="admin-panel"><h1><?php echo e($row[$config['title_field']]); ?></h1><?php foreach ($config['fields'] as $field) : if (in_array($field['type'], array('image','document'), true)) continue; ?><p><strong><?php echo e($field['label']); ?>:</strong><br><?php echo nl2br(e((string)($row[$field['name']] ?? ''))); ?></p><?php endforeach; ?><?php if (!empty($config['upload']) && !empty($row[$config['upload']])) : ?><div class="admin-image-preview"><img src="<?php echo site_url($row[$config['upload']]); ?>" alt=""></div><?php endif; ?><?php if (!empty($config['document']) && !empty($row[$config['document']])) : ?><p><a class="admin-button" href="<?php echo site_url($row[$config['document']]); ?>" target="_blank">Open Document</a></p><?php endif; ?></section></main></body></html><?php
}
