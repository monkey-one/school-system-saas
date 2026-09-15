<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

// Renders rich-text written in the admin panels (announcements, news) on
// public/portal pages. Only basic formatting tags survive and every attribute
// is removed, so scripts, event handlers and javascript: links cannot run.
final class SafeHtml
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><ul><ol><li><h2><h3><h4><blockquote><hr><table><thead><tbody><tr><th><td>';

    public static function basic(?string $html): HtmlString
    {
        $clean = strip_tags((string) $html, self::ALLOWED_TAGS);
        $clean = preg_replace('/<(\/?)([a-z0-9]+)\b[^>]*>/i', '<$1$2>', $clean);

        return new HtmlString($clean);
    }
}
