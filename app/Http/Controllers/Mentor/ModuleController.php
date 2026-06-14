<?php

namespace App\Http\Controllers\Mentor;

use App\Helpers\ContentRenderer;
use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ModuleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'video_title'       => 'nullable|string|max:200',
            'video_url'         => 'nullable|url',
            'video_description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'content'     => ContentRenderer::toPlain($request->input('content')),
        ];

        if (Schema::hasColumn('modules', 'video_title')) {
            $data['video_title'] = $request->input('video_title') ?? null;
        }
        if (Schema::hasColumn('modules', 'video_description')) {
            $data['video_description'] = $request->input('video_description') ?? null;
        }
        if (Schema::hasColumn('modules', 'video_url')) {
            $data['video_url'] = $request->input('video_url') ?? null;
        }

        Module::create($data);

        return redirect()
            ->route('mentor.index', ['tab' => 'modules'])
            ->with('success', 'Modul berhasil ditambahkan!');
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'video_title'       => 'nullable|string|max:200',
            'video_url'         => 'nullable|url',
            'video_description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'content'     => ContentRenderer::toPlain($request->input('content')),
        ];

        if (Schema::hasColumn('modules', 'video_title')) {
            $data['video_title'] = $request->input('video_title') ?? null;
        }
        if (Schema::hasColumn('modules', 'video_description')) {
            $data['video_description'] = $request->input('video_description') ?? null;
        }
        if (Schema::hasColumn('modules', 'video_url')) {
            $data['video_url'] = $request->input('video_url') ?? null;
        }

        $module->update($data);

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
}
