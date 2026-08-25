<?php
require_once __DIR__ . '/../includes/news-form.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
$categories = admin_get_categories();
$fieldErrors = array();
$generalErrors = array();
$story = array('title'=>'','slug'=>'','category'=>'','published_at'=>date('Y-m-d'),'location'=>'','excerpt'=>'','content'=>'','featured_image'=>'','is_featured'=>0,'status'=>'draft');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $story['title'] = trim((string) ($_POST['title'] ?? ''));
    $story['slug'] = trim((string) ($_POST['slug'] ?? ''));
    $story['category'] = trim((string) ($_POST['category'] ?? ''));
    $story['published_at'] = trim((string) ($_POST['published_at'] ?? ''));
    $story['location'] = trim((string) ($_POST['location'] ?? ''));
    $story['excerpt'] = trim((string) ($_POST['excerpt'] ?? ''));
    $story['content'] = admin_news_sanitize_html((string) ($_POST['content'] ?? ''));
    $story['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $action = (string) ($_POST['submit_action'] ?? 'draft');
    $story['status'] = $action === 'publish' ? 'published' : 'draft';

    $imagePath = '';
    if (!empty($_FILES['featured_image']['name'])) {
        try { $imagePath = admin_store_upload($_FILES['featured_image'], 'news'); }
        catch (Throwable $e) { $fieldErrors['featured_image'] = $e->getMessage(); }
    }
    $fieldErrors = array_merge($fieldErrors, admin_news_validate($story, $action, $imagePath !== ''));

    if (!$fieldErrors) {
        $slug = admin_slug_unique($pdo, 'news', $story['slug'] !== '' ? $story['slug'] : $story['title']);
        if ($story['is_featured']) { $pdo->exec('UPDATE news SET is_featured = 0'); }
        $publishedAt = $story['status'] === 'published' ? ($story['published_at'] ?: date('Y-m-d')) : null;
        $stmt = $pdo->prepare('INSERT INTO news (title, slug, excerpt, content, featured_image, category, location, status, is_featured, published_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)');
        $stmt->execute([$story['title'], $slug, $story['excerpt'], $story['content'], $imagePath, $story['category'], $story['location'], $story['status'], $story['is_featured'], $publishedAt]);
        $id = (int) $pdo->lastInsertId();
        if (!empty($_FILES['additional_images']['name'][0])) {
            foreach ($_FILES['additional_images']['name'] as $i => $name) {
                if ($name === '') { continue; }
                $file = array('name'=>$name,'type'=>$_FILES['additional_images']['type'][$i],'tmp_name'=>$_FILES['additional_images']['tmp_name'][$i],'error'=>$_FILES['additional_images']['error'][$i],'size'=>$_FILES['additional_images']['size'][$i]);
                try { $path = admin_store_upload($file, 'news'); $pdo->prepare('INSERT INTO news_images (news_id, image_path, sort_order) VALUES (?, ?, ?)')->execute([$id, $path, $i]); } catch (Throwable $e) { admin_flash('error', 'One additional image could not be uploaded: ' . $e->getMessage()); }
            }
        }
        admin_log($story['status'] === 'published' ? 'published' : 'created', 'news', $id, 'News story saved: ' . $story['title']);
        if ($story['status'] === 'published') {
            $_SESSION['admin_news_view_url'] = admin_news_story_url(array('slug' => $slug));
            admin_flash('success', 'News story published successfully.');
        } else {
            admin_flash('success', 'News draft saved.');
        }
        header('Location: ' . admin_url('news/edit.php?id=' . $id));
        exit;
    }
}
$adminTitle = 'Add News';
$activeNav = 'news';
require __DIR__ . '/../includes/admin-header.php';
$viewUrl = $_SESSION['admin_news_view_url'] ?? '';
unset($_SESSION['admin_news_view_url']);
?>
<section class="admin-panel">
    <?php foreach ($generalErrors as $error) : ?><div class="admin-alert admin-alert--error" style="margin:0 0 12px"><?php echo e($error); ?></div><?php endforeach; ?>
    <?php if ($viewUrl) : ?><div class="admin-actions" style="margin-bottom:16px"><a class="admin-button" href="<?php echo e($viewUrl); ?>" target="_blank" rel="noopener noreferrer">View on Website ↗</a></div><?php endif; ?>
    <?php admin_news_render_form($story, $categories, $fieldErrors, array(), false); ?>
</section>
<script src="<?php echo site_url('assets/js/admin-news-editor.js'); ?>?v=<?php echo rawurlencode((string) (@filemtime(__DIR__ . '/../../assets/js/admin-news-editor.js') ?: '1')); ?>"></script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>