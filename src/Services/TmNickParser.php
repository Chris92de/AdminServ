<?php
declare(strict_types=1);

namespace App\Services;

/**
 * TrackMania Formatting & Color Code Parser.
 */
class TmNickParser
{
    /**
     * Convert TrackMania formatted string ($w, $i, $s, $f00, etc.) to safe HTML.
     */
    public static function toHtml(string $text): string
    {
        if ($text === '') {
            return '';
        }

        // Replace literal '$$' with temporary placeholder
        $text = str_replace('$$', "\x01", $text);

        $len = strlen($text);
        $html = '';
        $openTags = 0;
        $currentColor = null;
        $isBold = false;
        $isItalic = false;
        $isShadow = false;

        $i = 0;
        while ($i < $len) {
            $char = $text[$i];

            if ($char === '$' && $i + 1 < $len) {
                $next = $text[$i + 1];
                $nextLower = strtolower($next);

                // 3-digit hex color: $fff
                if (preg_match('/^\$([0-9a-fA-F]{3})/', substr($text, $i, 4), $m)) {
                    $r = hexdec($m[1][0]) * 17;
                    $g = hexdec($m[1][1]) * 17;
                    $b = hexdec($m[1][2]) * 17;
                    if ($openTags > 0) {
                        $html .= str_repeat('</span>', $openTags);
                        $openTags = 0;
                    }
                    $html .= "<span style=\"color: rgb({$r},{$g},{$b});\">";
                    $openTags++;
                    $i += 4;
                    continue;
                }

                // Formatting modifiers
                if ($nextLower === 'i') {
                    $html .= "<span style=\"font-style: italic;\">";
                    $openTags++;
                    $i += 2;
                    continue;
                }
                if ($nextLower === 'w' || $nextLower === 'o') {
                    $html .= "<span style=\"font-weight: 700;\">";
                    $openTags++;
                    $i += 2;
                    continue;
                }
                if ($nextLower === 's') {
                    $html .= "<span style=\"text-shadow: 1px 1px 2px rgba(0,0,0,0.8);\">";
                    $openTags++;
                    $i += 2;
                    continue;
                }
                if ($nextLower === 'z' || $nextLower === 'g') {
                    if ($openTags > 0) {
                        $html .= str_repeat('</span>', $openTags);
                        $openTags = 0;
                    }
                    $i += 2;
                    continue;
                }

                // Skip links $l, $h, etc.
                if ($nextLower === 'l' || $nextLower === 'h') {
                    // Match link target e.g. $l[url]
                    if ($i + 2 < $len && $text[$i + 2] === '[') {
                        $closePos = strpos($text, ']', $i + 2);
                        if ($closePos !== false) {
                            $i = $closePos + 1;
                            continue;
                        }
                    }
                    $i += 2;
                    continue;
                }

                // If unrecognized $ code, skip $
                $i += 2;
                continue;
            }

            $html .= htmlspecialchars($char, ENT_QUOTES, 'UTF-8');
            $i++;
        }

        if ($openTags > 0) {
            $html .= str_repeat('</span>', $openTags);
        }

        // Restore literal $$
        return str_replace("\x01", '$', $html);
    }

    /**
     * Strip all TrackMania formatting and color codes.
     */
    public static function strip(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = str_replace('$$', "\x01", $text);
        // Remove 3-digit hex colors
        $text = preg_replace('/\$[0-9a-fA-F]{3}/', '', $text);
        // Remove link tags $l[...] or $h[...]
        $text = preg_replace('/\$[lh]\[.*?\]/i', '', $text);
        // Remove single character control codes ($w, $o, $i, $s, $t, $m, $n, $z, $g, $l, $h, etc.)
        $text = preg_replace('/\$[a-zA-Z]/', '', $text);

        return str_replace("\x01", '$', (string)$text);
    }
}
