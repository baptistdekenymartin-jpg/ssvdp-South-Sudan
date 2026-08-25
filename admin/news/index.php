<?php
require_once __DIR__ . '/../includes/auth.php';
$adminUser = admin_require_auth();
$pdo = admin_require_db();
admin_require_csrf();

function admin_news_delete_file_if_unused(string $path): void
{
    $path = trim($path);
    if ($path === '' || preg_match('#^[a-z][a-z0-9+.-]*://#i', $path)) {
        return;
    }

    $root = realpath(dirname(__DIR__, 2));
    if (!$root) {
        return;
    }

    $relativePath = ltrim(str_replace('\\', '/', $path), '/');
    if ($relativePath === '' || str_contains($relativePath, '..')) {
        return;
    }

    $target = realpath($root . DIRECTORY_SEPARATOR . $relativePath);
    if (!$target || !is_file($target)) {
        return;
    }

    $rootPrefix = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (!str_starts_with($target, $rootPrefix)) {
        return;
    }

    @unlink($target);
}

function admin_news_image_is_used_elsewhere(PDO $pdo, string $path, int $newsId): bool
{
    if ($path === '') {
        return false;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM news WHERE featured_image = ? AND id <> ?');
    $stmt->execute([$path, $newsId]);
    if ((int) $stmt->fetchColumn() > 0) {
        return true;
    }

    if (ssvdp_table_exists($pdo, 'news_images')) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM news_images WHERE image_path = ? AND news_id <> ?');
        $stmt->execute([$path, $newsId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    return false;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    if ($id > 0) {
        if ($action === 'publish') {
            $pdo->prepare("UPDATE news SET status = 'published', published_at = COALESCE(published_at, NOW()) WHERE id = ?")->execute([$id]);
            admin_log('published', 'news', $id, 'News story published.');
            admin_flash('success', 'News story published.');
        } elseif ($action === 'unpublish') {
            $pdo->prepare("UPDATE news SET status = 'draft' WHERE id = ?")->execute([$id]);
            admin_log('unpublished', 'news', $id, 'News story unpublished.');
            admin_flash('success', 'News story unpublished.');
        } elseif ($action === 'archive') {
            $pdo->prepare('UPDATE news SET archived_at = NOW(), status = \'draft\' WHERE id = ?')->execute([$id]);
            admin_log('archived', 'news', $id, 'News story archived.');
            admin_flash('success', 'News story archived.');
        } elseif ($action === 'delete') {
            if (!admin_is_administrator($adminUser)) {
                admin_forbidden();
            }

            $stmt = $pdo->prepare('SELECT id, title, featured_image FROM news WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $storyToDelete = $stmt->fetch();

            if ($storyToDelete) {
                $paths = array();
                if (!empty($storyToDelete['featured_image'])) {
                    $paths[] = (string) $storyToDelete['featured_image'];
                }

                if (ssvdp_table_exists($pdo, 'news_images')) {
                    $imageStmt = $pdo->prepare('SELECT image_path FROM news_images WHERE news_id = ?');
                    $imageStmt->execute([$id]);
                    foreach ($imageStmt->fetchAll() as $imageRow) {
                        if (!empty($imageRow['image_path'])) {
                            $paths[] = (string) $imageRow['image_path'];
                        }
                    }
                }

                $pathsToDelete = array();
                foreach (array_unique($paths) as $imagePath) {
                    if (!admin_news_image_is_used_elsewhere($pdo, $imagePath, $id)) {
                        $pathsToDelete[] = $imagePath;
                    }
                }

                $pdo->beginTransaction();
                try {
                    if (ssvdp_table_exists($pdo, 'news_images')) {
                        $pdo->prepare('DELETE FROM news_images WHERE news_id = ?')->execute([$id]);
                    }
                    $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
                    admin_log('deleted', 'news', $id, 'News story deleted permanently: ' . (string) $storyToDelete['title']);
                    $pdo->commit();
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    admin_flash('error', 'Could not delete the news story. Please try again.');
                    header('Location: ' . admin_url('news/'));
                    exit;
                }

                foreach ($pathsToDelete as $imagePath) {
                    admin_news_delete_file_if_unused($imagePath);
                }

                admin_flash('success', 'News story deleted successfully.');
            }
        }
    }
    header('Location: ' . admin_url('news/'));
    exit;
}

$search = trim((string) ($_GET['search'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$where = array('archived_at IS NULL');
$params = array();
if ($search !== '') { $where[] = '(title LIKE ? OR excerpt LIKE ?)'; $params[] = '%' . $search . '%'; $params[] = '%' . $search . '%'; }
if ($category !== '') { $where[] = 'category = ?'; $params[] = $category; }
if ($status !== '') { $where[] = 'status = ?'; $params[] = $status; }
$stmt = $pdo->prepare('SELECT * FROM news WHERE ' . implode(' AND ', $where) . ' ORDER BY updated_at DESC, id DESC');
$stmt->execute($params);
$stories = $stmt->fetchAll();
$categories = admin_get_categories();
$adminTitle = 'News & Updates';
$activeNav = 'news';
require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar">
    <form class="admin-filters" method="get">
        <input class="admin-input" style="width:220px" name="search" placeholder="Search news" value="<?php echo e($search); ?>">
        <select class="admin-select" style="width:220px" name="category"><option value="">All categories</option><?php foreach ($categories as $cat) : ?><option value="<?php echo e($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option><?php endforeach; ?></select>
        <select class="admin-select" style="width:150px" name="status"><option value="">All statuses</option><option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option><option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option></select>
        <button class="admin-button admin-button--light" type="submit">Filter</button>
    </form>
    <a class="admin-button" href="<?php echo admin_url('news/add.php'); ?>"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add News</a>
</div>
<section class="admin-table-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Thumbnail</th><th>Title</th><th>Category</th><th>Date</th><th>Status</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($stories as $story) : ?>
                <tr>
                    <td><?php if ($story['featured_image']) : ?><img class="admin-thumb" src="<?php echo site_url($story['featured_image']); ?>" alt=""><?php endif; ?></td>
                    <td><strong><?php echo e($story['title']); ?></strong></td>
                    <td><?php echo e($story['category']); ?></td>
                    <td><?php echo e(ssvdp_format_date($story['published_at'] ?: $story['created_at'])); ?></td>
                    <td><span class="admin-status admin-status--<?php echo e($story['status']); ?>"><?php echo e(ucfirst($story['status'])); ?></span></td>
                    <td><?php echo ((int) $story['is_featured'] === 1) ? 'Yes' : 'No'; ?></td>
                    <td><div class="admin-row-actions">
                        <a href="<?php echo admin_url('news/edit.php?id=' . (int) $story['id']); ?>">Edit</a>
                        <a href="<?php echo admin_url('news/preview.php?id=' . (int) $story['id']); ?>" target="_blank">Preview</a>
                        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $story['id']; ?>"><input type="hidden" name="action" value="<?php echo $story['status'] === 'published' ? 'unpublish' : 'publish'; ?>"><button type="submit"><?php echo $story['status'] === 'published' ? 'Unpublish' : 'Publish'; ?></button></form>
                        <form method="post" onsubmit="return confirm('Archive this news story? It will no longer appear publicly.');"><input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $story['id']; ?>"><input type="hidden" name="action" value="archive"><button type="submit">Archive</button></form>
                        <?php if (admin_is_administrator($adminUser)) : ?><button class="admin-row-delete-button" type="button" data-news-delete-trigger data-news-id="<?php echo (int) $story['id']; ?>" data-news-title="<?php echo e($story['title']); ?>">Delete</button><?php endif; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$stories) : ?><tr><td colspan="7">No news stories found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<dialog class="admin-delete-dialog" data-news-delete-dialog>
    <form method="post" data-news-delete-form>
        <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="">
        <h2>Delete News Story</h2>
        <p>Are you sure you want to delete this news story?</p>
        <p><strong data-news-delete-title></strong></p>
        <div class="admin-delete-dialog__actions">
            <button class="admin-button admin-button--light" type="button" data-news-delete-cancel>Cancel</button>
            <button class="admin-button admin-button--danger" type="submit">Delete Permanently</button>
        </div>
    </form>
</dialog>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dialog = document.querySelector('[data-news-delete-dialog]');
    if (!dialog) { return; }

    var form = dialog.querySelector('[data-news-delete-form]');
    var idInput = form.querySelector('input[name="id"]');
    var titleNode = dialog.querySelector('[data-news-delete-title]');
    var cancelButton = dialog.querySelector('[data-news-delete-cancel]');

    document.querySelectorAll('[data-news-delete-trigger]').forEach(function (button) {
        button.addEventListener('click', function () {
            idInput.value = button.getAttribute('data-news-id') || '';
            titleNode.textContent = button.getAttribute('data-news-title') || '';
            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            } else if (window.confirm('Are you sure you want to delete this news story?\n\n' + titleNode.textContent)) {
                form.submit();
            }
        });
    });

    cancelButton.addEventListener('click', function () {
        dialog.close();
    });
});
</script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
