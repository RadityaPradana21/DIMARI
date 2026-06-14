<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ModuleManagementController extends Controller
{
    // List semua module (dengan pencarian & pagination)
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 15);

        $modulesQuery = Module::query();

        if ($q !== '') {
            $modulesQuery->where(function ($wr) use ($q) {
                $wr->where('title', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%")
                   ->orWhere('content', 'like', "%{$q}%");
            });
        }

        $modules = $modulesQuery->orderByDesc('created_at')
                                ->paginate(max(5, min(100, $perPage)))
                                ->withQueryString();

        return view('admin.modules.index', compact('modules', 'q'));
    }

    // Form tambah module    
    public function create()
    {
        return view('admin.modules.create');
    }

    // Simpan module baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_title'       => 'nullable|string|max:200',
            'video_url'         => 'nullable|url',
            'video_description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
        ];

        if (Schema::hasColumn('modules', 'video_title')) {
            $data['video_title'] = $validated['video_title'] ?? null;
        }
        if (Schema::hasColumn('modules', 'video_description')) {
            $data['video_description'] = $validated['video_description'] ?? null;
        }
        if (Schema::hasColumn('modules', 'video_url')) {
            $data['video_url'] = $validated['video_url'] ?? null;
        }

        Module::create($data);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Module berhasil ditambahkan.');
    }

    // Detail module
    public function show(Module $module)
    {
        return view('admin.modules.show', compact('module'));
    }

    // Form edit module
    public function edit(Module $module)
    {
        return view('admin.modules.edit', compact('module'));
    }

    // Update module
    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_title'       => 'nullable|string|max:200',
            'video_url'         => 'nullable|url',
            'video_description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
        ];

        if (Schema::hasColumn('modules', 'video_title')) {
            $data['video_title'] = $validated['video_title'] ?? null;
        }
        if (Schema::hasColumn('modules', 'video_description')) {
            $data['video_description'] = $validated['video_description'] ?? null;
        }
        if (Schema::hasColumn('modules', 'video_url')) {
            $data['video_url'] = $validated['video_url'] ?? null;
        }

        $module->update($data);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Module berhasil diperbarui.');
    }

    // Hapus module
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module berhasil dihapus.');
    }
}
