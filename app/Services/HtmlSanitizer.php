<?php

namespace App\Services;

class HtmlSanitizer
{
    /**
     * Allowed HTML tags for rich-text content.
     */
    protected static array $allowedTags = [
        'p', 'br', 'hr',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'strike',
        'blockquote', 'pre', 'code',
        'ul', 'ol', 'li',
        'a', 'img', 'span', 'div',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ];

    /**
     * Allowed attributes per tag.
     */
    protected static array $allowedAttributes = [
        'a' => ['href', 'title', 'target', 'rel', 'class'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'class'],
        '*' => ['class', 'id', 'style'],
    ];

    /**
     * Sanitize rich-text HTML string.
     * Removes dangerous tags (script, iframe, object, embed, etc.),
     * inline event handlers (onload, onerror, onclick, etc.),
     * and dangerous URL schemes (javascript:, vbscript:, data:text/html).
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Remove dangerous blocks completely (tags + their inner contents)
        $dangerousBlocks = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/<style\b[^>]*>(.*?)<\/style>/is',
            '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
            '/<object\b[^>]*>(.*?)<\/object>/is',
            '/<embed\b[^>]*>(.*?)<\/embed>/is',
            '/<applet\b[^>]*>(.*?)<\/applet>/is',
            '/<form\b[^>]*>(.*?)<\/form>/is',
        ];
        $cleaned = preg_replace($dangerousBlocks, '', $html);

        // 2. Strip disallowed HTML tags
        $tagList = '<'.implode('><', static::$allowedTags).'>';
        $cleaned = strip_tags($cleaned, $tagList);

        // 3. Remove inline event handlers (on* e.g. onclick, onload, onerror, etc.)
        $cleaned = preg_replace('/\s*on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $cleaned);

        // 4. Remove javascript:, vbscript:, and dangerous data: schemes from href/src
        $cleaned = preg_replace_callback(
            '/\b(href|src)\s*=\s*(["\'])(.*?)\2/i',
            function ($matches) {
                $attr = strtolower($matches[1]);
                $quote = $matches[2];
                $url = trim($matches[3]);

                // Check for dangerous protocol
                if (preg_match('/^(javascript|vbscript|data:(?!image\/))/i', $url)) {
                    return "{$attr}={$quote}#{$quote}";
                }

                return "{$attr}={$quote}{$url}{$quote}";
            },
            $cleaned
        );

        // 5. Remove expression() and behavior: in style attributes
        $cleaned = preg_replace_callback(
            '/\bstyle\s*=\s*(["\'])(.*?)\1/i',
            function ($matches) {
                $quote = $matches[1];
                $style = $matches[2];

                if (preg_match('/(expression|behavior|javascript:|url\s*\()/i', $style)) {
                    return '';
                }

                return "style={$quote}{$style}{$quote}";
            },
            $cleaned
        );

        return trim($cleaned);
    }
}
