<?php

declare(strict_types=1);

function ssvdp_news_sanitize_html(string $html): string
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
