<?php

namespace App\Helpers;

/**
 * Konversi konten plain text (dengan penanda markdown-like)
 * ke HTML untuk tampilan, dan sebaliknya.
 *
 * Dipanggil dari:
 *  - MateriController  → tampilkan konten ke user
 *  - mentor/dashboard  → tampilkan konten di WYSIWYG editor saat edit
 */
class ContentRenderer
{
    /**
     * Plain text dengan penanda → HTML siap tampil.
     *
     * Penanda yang didukung (dihasilkan oleh ModuleController::sanitizeContent):
     *   ## Teks        → <h2>Teks</h2>
     *   ### Teks       → <h3>Teks</h3>
     *   #### Teks      → <h4>Teks</h4>
     *   - item         → <ul><li>item</li></ul>
     *   **teks**       → <strong>teks</strong>
     *   _teks_         → <em>teks</em>
     *   ---            → <hr>
     *   baris kosong   → pemisah paragraf
     */
    public static function toHtml(?string $raw): string
    {
        if (empty(trim((string) $raw))) {
            return '<p class="text-muted">Konten modul belum tersedia.</p>';
        }

        // Kalau sudah HTML (data lama sebelum migrasi), render langsung
        if (preg_match('/<(h[2-4]|p|ul|ol|li|strong|em|blockquote)\b/i', $raw)) {
            return $raw;
        }

        $lines      = explode("\n", $raw);
        $html       = '';
        $ulOpen     = false;
        $paraBuf    = [];

        $flushPara = function () use (&$paraBuf, &$html) {
            if (!empty($paraBuf)) {
                $text = implode(' ', $paraBuf);
                $html .= '<p>' . self::inlineMarkup($text) . '</p>' . "\n";
                $paraBuf = [];
            }
        };

        $closeUl = function () use (&$ulOpen, &$html) {
            if ($ulOpen) { $html .= '</ul>' . "\n"; $ulOpen = false; }
        };

        foreach ($lines as $line) {
            $t = rtrim($line);

            if ($t === '') {
                $flushPara(); $closeUl(); continue;
            }

            if (preg_match('/^## (.+)$/', $t, $m)) {
                $flushPara(); $closeUl();
                $html .= '<h2>' . e(trim($m[1])) . '</h2>' . "\n"; continue;
            }
            if (preg_match('/^### (.+)$/', $t, $m)) {
                $flushPara(); $closeUl();
                $html .= '<h3>' . e(trim($m[1])) . '</h3>' . "\n"; continue;
            }
            if (preg_match('/^#### (.+)$/', $t, $m)) {
                $flushPara(); $closeUl();
                $html .= '<h4>' . e(trim($m[1])) . '</h4>' . "\n"; continue;
            }
            if ($t === '---') {
                $flushPara(); $closeUl();
                $html .= '<hr>' . "\n"; continue;
            }
            if (preg_match('/^[-*] (.+)$/', $t, $m)) {
                $flushPara();
                if (!$ulOpen) { $html .= '<ul>' . "\n"; $ulOpen = true; }
                $html .= '<li>' . self::inlineMarkup(trim($m[1])) . '</li>' . "\n"; continue;
            }

            $closeUl();
            $paraBuf[] = $t;
        }

        $flushPara(); $closeUl();

        return $html ?: '<p class="text-muted">Konten modul belum tersedia.</p>';
    }

    /**
     * HTML dari WYSIWYG editor → plain text dengan penanda.
     * Dipanggil oleh ModuleController sebelum simpan ke DB.
     */
    public static function toPlain(?string $html): ?string
    {
        if (empty($html)) return null;
        if (strip_tags($html) === $html) return trim($html);

        $t = $html;
        $t = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n\n## $1\n", $t);
        $t = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n\n### $1\n", $t);
        $t = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "\n\n#### $1\n", $t);
        $t = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "\n- $1", $t);
        $t = preg_replace('/<\/(ul|ol)>/i', "\n", $t);
        $t = preg_replace('/<(ul|ol)[^>]*>/i', '', $t);
        $t = preg_replace('/<\/p>/i', "\n\n", $t);
        $t = preg_replace('/<p[^>]*>/i', '', $t);
        $t = preg_replace('/<strong[^>]*>(.*?)<\/strong>/is', '**$1**', $t);
        $t = preg_replace('/<b[^>]*>(.*?)<\/b>/is', '**$1**', $t);
        $t = preg_replace('/<em[^>]*>(.*?)<\/em>/is', '_$1_', $t);
        $t = preg_replace('/<i[^>]*>(.*?)<\/i>/is', '_$1_', $t);
        $t = preg_replace('/<br\s*\/?>/i', "\n", $t);
        $t = preg_replace('/<hr\s*\/?>/i', "\n---\n", $t);
        $t = strip_tags($t);
        $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = preg_replace('/\n{3,}/', "\n\n", $t);
        return trim($t);
    }

    /** Terapkan **bold** dan _italic_ pada satu baris teks. */
    private static function inlineMarkup(string $text): string
    {
        $text = e($text);
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/_(.+?)_/', '<em>$1</em>', $text);
        return $text;
    }
}
