<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleManagementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        Module::create([
            'title'       => $request->title,
            'description' => $request->description,
            'content'     => $this->sanitizeContent($request->content),
        ]);

        return redirect()
            ->route('mentor.index', ['tab' => 'modules'])
            ->with('success', 'Modul berhasil ditambahkan!');
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        $module->update([
            'title'       => $request->title,
            'description' => $request->description,
            'content'     => $this->sanitizeContent($request->content),
        ]);

        return redirect()
            ->route('mentor.index', ['tab' => 'modules'])
            ->with('success', 'Modul berhasil diupdate!');
    }

    public function destroy(Module $module)
    {
        foreach ($module->questions as $question) {
            $question->options()->delete();
            $question->delete();
        }
        $module->delete();

        return redirect()
            ->route('mentor.index', ['tab' => 'modules'])
            ->with('success', 'Modul dihapus.');
    }

    /**
     * Konversi HTML dari WYSIWYG editor ke plain text terstruktur.
     * Simpan sebagai plain text dengan penanda sederhana agar tidak ada HTML di DB.
     */
    private function sanitizeContent(?string $html): ?string
    {
        if (empty($html)) return null;

        // Sudah plain text? langsung kembalikan
        if (strip_tags($html) === $html) return trim($html);

        // Konversi tag HTML ke markdown-like plain text
        $text = $html;

        // Heading
        $text = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n\n## $1\n", $text);
        $text = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n\n### $1\n", $text);
        $text = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "\n\n#### $1\n", $text);

        // List items
        $text = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "\n- $1", $text);
        $text = preg_replace('/<\/(ul|ol)>/i', "\n", $text);
        $text = preg_replace('/<(ul|ol)[^>]*>/i', "", $text);

        // Paragraf
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<p[^>]*>/i', "", $text);

        // Bold/italic → simbol
        $text = preg_replace('/<strong[^>]*>(.*?)<\/strong>/is', "**$1**", $text);
        $text = preg_replace('/<b[^>]*>(.*?)<\/b>/is', "**$1**", $text);
        $text = preg_replace('/<em[^>]*>(.*?)<\/em>/is', "_$1_", $text);
        $text = preg_replace('/<i[^>]*>(.*?)<\/i>/is', "_$1_", $text);

        // Line breaks
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<hr\s*\/?>/i', "\n---\n", $text);

        // Hapus semua tag HTML yang tersisa
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Bersihkan spasi berlebih
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }
}
