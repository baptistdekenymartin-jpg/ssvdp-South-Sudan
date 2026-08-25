<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function admin_news_sanitize_html(string $html): string
{
    $html = trim($html);
    if ($html === '') { return ''; }
    if (!preg_match('/<[a-zA-Z][\s\S]*>/', $html)) {
        $paragraphs = array_filter(array_map('trim', preg_split('/\R\R+/', $html) ?: array()));
        return implode('', array_map(static fn($paragraph): string => '<p>' . e($paragraph) . '</p>', $paragraphs));
    }
    $html = preg_replace('#<(script|iframe|object|embed|style)\b[^>]*>.*?</\1>#is', '', $html) ?? '';
    $html = preg_replace('#<\/?(script|iframe|object|embed|style)\b[^>]*>#i', '', $html) ?? '';
    $html = strip_tags($html, '<p><br><strong><b><em><i><u><h2><h3><ul><ol><li><blockquote><a>');
    $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
    $html = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
    $html = preg_replace_callback('/<a\b([^>]*)>/i', static function (array $match): string {
        $attrs = $match[1];
        if (preg_match('/href\s*=\s*("|\')([^"\']+)("|\')/i', $attrs, $hrefMatch)) {
            $href = trim(html_entity_decode($hrefMatch[2], ENT_QUOTES, 'UTF-8'));
            if (preg_match('#^(https?:|mailto:|tel:|/)#i', $href)) {
                $safeHref = e($href);
                $extra = preg_match('#^https?://#i', $href) ? ' target="_blank"' : '';
                return '<a href="' . $safeHref . '" rel="noopener noreferrer"' . $extra . '>';
            }
        }
        return '<a>';
    }, $html) ?? '';
    $html = preg_replace('/<(p|strong|b|em|i|u|h2|h3|ul|ol|li|blockquote)\b[^>]*>/i', '<$1>', $html) ?? '';
    return trim($html);
}
function admin_news_plain_text(string $html): string
{
    return trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
}

function admin_news_validate(array $story, string $action, bool $hasFeaturedImage): array
{
    if ($action !== 'publish') { return array(); }
    $errors = array();
    if ($story['title'] === '') { $errors['title'] = 'Enter the news title before publishing.'; }
    if ($story['category'] === '') { $errors['category'] = 'Choose a category before publishing.'; }
    if ($story['published_at'] === '') { $errors['published_at'] = 'Choose the publication date before publishing.'; }
    if ($story['excerpt'] === '') { $errors['excerpt'] = 'Add a short summary before publishing.'; }
    if (admin_news_plain_text($story['content']) === '') { $errors['content'] = 'Add the full story before publishing.'; }
    if (!$hasFeaturedImage) { $errors['featured_image'] = 'Add a featured image before publishing.'; }
    return $errors;
}

function admin_news_render_field_error(array $fieldErrors, string $field): void
{
    if (!empty($fieldErrors[$field])) { echo '<small class="admin-field-error">' . e($fieldErrors[$field]) . '</small>'; }
}

function admin_news_story_url(array $story): string
{
    return site_url('news-detail.php?slug=' . rawurlencode((string) $story['slug']));
}

function admin_news_render_form(array $story, array $categories, array $fieldErrors, array $additionalImages, bool $isEdit, int $id = 0): void
{
    $content = (string) ($story['content'] ?? '');
    ?>
    <form class="admin-form admin-news-form" method="post" enctype="multipart/form-data" data-news-form>
        <input type="hidden" name="csrf_token" value="<?php echo e(admin_csrf_token()); ?>">
        <?php if ($isEdit) : ?><input type="hidden" name="id" value="<?php echo (int) $id; ?>"><?php endif; ?>
        <textarea class="admin-rich-source" name="content" data-rich-source required><?php echo e($content); ?></textarea>
        <div class="admin-form-grid">
            <div class="admin-field"><label>News Title</label><input class="admin-input" name="title" data-slug-title value="<?php echo e($story['title']); ?>"><small>Recommended maximum: 100-120 characters so public cards keep their approved size.</small><?php admin_news_render_field_error($fieldErrors, 'title'); ?></div>
            <div class="admin-field"><label>Slug</label><input class="admin-input" name="slug" data-slug-input value="<?php echo e($story['slug']); ?>"><small>Generated from the title. You can edit it manually.</small></div>
        </div>
        <div class="admin-form-grid">
            <div class="admin-field"><label>Category</label><select class="admin-select" name="category"><option value="">Select category</option><?php foreach ($categories as $cat) : ?><option value="<?php echo e($cat); ?>" <?php echo $story['category']===$cat?'selected':''; ?>><?php echo e($cat); ?></option><?php endforeach; ?></select><?php admin_news_render_field_error($fieldErrors, 'category'); ?></div>
            <div class="admin-field"><label>Publication Date</label><input class="admin-input" type="date" name="published_at" value="<?php echo e(substr((string) $story['published_at'], 0, 10)); ?>"><?php admin_news_render_field_error($fieldErrors, 'published_at'); ?></div>
        </div>
        <div class="admin-field"><label>Location</label><input class="admin-input" name="location" value="<?php echo e($story['location']); ?>"></div>
        <div class="admin-field"><label>Short Summary</label><textarea class="admin-textarea admin-summary-textarea" name="excerpt" data-summary-counter><?php echo e($story['excerpt']); ?></textarea><small><span data-summary-count>0</span> characters. Aim for 160-250 characters; longer summaries are shortened visually on public overview pages.</small><?php admin_news_render_field_error($fieldErrors, 'excerpt'); ?></div>
        <div class="admin-field"><label>Full Story</label><small>Use safe story content only. Public templates control layout, spacing, colors and card sizes.</small><div class="admin-rich-editor" data-rich-editor><div class="admin-rich-toolbar" role="toolbar" aria-label="Story formatting"><button type="button" data-cmd="bold"><strong>B</strong></button><button type="button" data-cmd="italic"><em>I</em></button><button type="button" data-cmd="underline"><u>U</u></button><button type="button" data-block="h2">H2</button><button type="button" data-block="h3">H3</button><button type="button" data-block="p">P</button><button type="button" data-cmd="insertUnorderedList">Bullets</button><button type="button" data-cmd="insertOrderedList">Numbers</button><button type="button" data-block="blockquote">Quote</button><button type="button" data-link>Link</button><button type="button" data-cmd="undo">Undo</button><button type="button" data-cmd="redo">Redo</button><button type="button" data-clear>Clear</button></div><div class="admin-rich-editable" data-rich-editable contenteditable="true"><?php echo $content; ?></div></div><?php admin_news_render_field_error($fieldErrors, 'content'); ?></div>
        <div class="admin-form-grid">
            <div class="admin-field"><label><?php echo $isEdit ? 'Change Featured Image' : 'Featured Image'; ?></label><?php if (!empty($story['featured_image'])) : ?><div class="admin-news-current-image"><img src="<?php echo site_url($story['featured_image']); ?>" alt="Current featured image"><span>Current featured image</span></div><?php endif; ?><input class="admin-input" type="file" name="featured_image" data-featured-image accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><div class="admin-file-preview" data-featured-preview hidden><img alt="Selected featured image preview"><span data-file-name></span><button type="button" data-remove-featured>Remove</button></div><?php admin_news_render_field_error($fieldErrors, 'featured_image'); ?></div>
            <div class="admin-field"><label><?php echo $isEdit ? 'Add Additional Images' : 'Additional Images'; ?></label><input class="admin-input" type="file" name="additional_images[]" data-additional-images multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small><span data-additional-count>0</span> selected. Remove any image before saving if needed.</small><div class="admin-selected-images" data-additional-preview></div></div>
        </div>
        <?php if ($additionalImages) : ?><div class="admin-field"><label>Existing Additional Images</label><div class="admin-photo-grid"><?php foreach ($additionalImages as $image) : ?><article class="admin-photo-card"><img src="<?php echo site_url($image['image_path']); ?>" alt=""><div><?php echo e($image['caption'] ?: 'Additional image'); ?></div></article><?php endforeach; ?></div></div><?php endif; ?>
        <label class="admin-checkbox-help"><input type="checkbox" name="is_featured" value="1" <?php echo (int) $story['is_featured'] === 1 ? 'checked' : ''; ?>> <span>Featured Story<small>Only one story can occupy the large Featured Story position. Publishing this as featured automatically removes the previous featured story.</small></span></label>
        <div class="admin-actions admin-news-actions"><button class="admin-button admin-button--light" name="submit_action" value="draft" type="submit">Save Draft</button><button class="admin-button admin-button--outline" name="submit_action" value="preview" type="button" data-news-preview>Preview</button><button class="admin-button" name="submit_action" value="publish" type="submit">Publish</button></div>
    </form>
    <?php
}