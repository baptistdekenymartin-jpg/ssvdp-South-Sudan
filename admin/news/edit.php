<?php
require_once __DIR__ . '/../includes/news-form.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM news WHERE id = ? AND archived_at IS NULL LIMIT 1');
$stmt->execute([$id]);
$story = $stmt->fetch();
if (!$story) { admin_flash('error', 'News story not found.'); header('Location: ' . admin_url('news/')); exit; }
$categories = admin_get_categories();
$fieldErrors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $data = array(
        'title' => trim((string) ($_POST['title'] ?? '')),
        'slug' => trim((string) ($_POST['slug'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')),
        'published_at' => trim((string) ($_POST['published_at'] ?? '')),
        'location' => trim((string) ($_POST['location'] ?? '')),
        'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
        'content' => admin_news_sanitize_html((string) ($_POST['content'] ?? '')),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'status' => (string) ($_POST['submit_action'] ?? $story['status']) === 'publish' ? 'published' : 'draft'
    );
    $imagePath = $story['featured_image'];
    if (!empty($_FILES['featured_image']['name'])) {
        try { $imagePath = admin_store_upload($_FILES['featured_image'], 'news'); }
        catch (Throwable $e) { $fieldErrors['featured_image'] = $e->getMessage(); }
    }
    $fieldErrors = array_merge($fieldErrors, admin_news_validate($data, (string) ($_POST['submit_action'] ?? 'draft'), $imagePath !== ''));
    if (!$fieldErrors) {
        $slug = admin_slug_unique($pdo, 'news', $data['slug'] !== '' ? $data['slug'] : $data['title'], $id);
        if ($data['is_featured']) { $pdo->prepare('UPDATE news SET is_featured = 0 WHERE id <> ?')->execute([$id]); }
        $publishedAt = $data['status'] === 'published' ? ($data['published_at'] ?: date('Y-m-d')) : null;
        $stmt = $pdo->prepare('UPDATE news SET title=?, slug=?, excerpt=?, content=?, featured_image=?, category=?, location=?, status=?, is_featured=?, published_at=? WHERE id=?');
        $stmt->execute([$data['title'], $slug, $data['excerpt'], $data['content'], $imagePath, $data['category'], $data['location'], $data['status'], $data['is_featured'], $publishedAt, $id]);
        if (!empty($_FILES['additional_images']['name'][0])) {
            foreach ($_FILES['additional_images']['name'] as $i => $name) {
                if ($name === '') { continue; }
                $file = array('name'=>$name,'type'=>$_FILES['additional_images']['type'][$i],'tmp_name'=>$_FILES['additional_images']['tmp_name'][$i],'error'=>$_FILES['additional_images']['error'][$i],'size'=>$_FILES['additional_images']['size'][$i]);
                try { $path = admin_store_upload($file, 'news'); $pdo->prepare('INSERT INTO news_images (news_id, image_path, sort_order) VALUES (?, ?, ?)')->execute([$id, $path, $i]); } catch (Throwable $e) { admin_flash('error', 'One additional image could not be uploaded: ' . $e->getMessage()); }
            }
        }
        admin_log($data['status'] === 'published' ? 'published' : 'edited', 'news', $id, 'News story edited: ' . $data['title']);
        if ($data['status'] === 'published') {
            $_SESSION['admin_news_view_url'] = admin_news_story_url(array('slug' => $slug));
            admin_flash('success', 'News story published successfully.');
        } else {
            admin_flash('success', 'News story updated.');
        }
        header('Location: ' . admin_url('news/edit.php?id=' . $id));
        exit;
    }
    $story = array_merge($story, $data, array('featured_image' => $imagePath));
}
$images = $pdo->prepare('SELECT * FROM news_images WHERE news_id = ? ORDER BY sort_order, id');
$images->execute([$id]);
$additionalImages = $images->fetchAll();
$adminTitle = 'Edit News';
$activeNav = 'news';
require __DIR__ . '/../includes/admin-header.php';
$viewUrl = $_SESSION['admin_news_view_url'] ?? '';
unset($_SESSION['admin_news_view_url']);
?>
<section class="admin-panel">
    <?php if ($viewUrl) : ?><div class="admin-actions" style="margin-bottom:16px"><a class="admin-button" href="<?php echo e($viewUrl); ?>" target="_blank" rel="noopener noreferrer">View on Website ↗</a></div><?php endif; ?>
    <?php admin_news_render_form($story, $categories, $fieldErrors, $additionalImages, true, $id); ?>
</section>
<script src="<?php echo site_url('assets/js/admin-news-editor.js'); ?>?v=<?php echo rawurlencode((string) (@filemtime(__DIR__ . '/../../assets/js/admin-news-editor.js') ?: '1')); ?>"></script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>