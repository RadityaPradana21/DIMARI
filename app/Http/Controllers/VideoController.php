<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua modul yang memiliki video_url (jika kolom ada)
        // Jika belum ada kolom video_url di tabel modules, kirim koleksi kosong
        // dan tampilkan placeholder di view.
        $modules = Module::orderBy('id')->get();

        // Filter modul yang punya video (jika kolom video_url sudah ada di DB)
        // Hapus baris di bawah jika kolom belum ada, gunakan $modules langsung.
        $videoModules = $modules->filter(fn($m) => !empty($m->video_url ?? null));

        // Module aktif
        $selectedId   = $request->integer('module', $modules->first()?->id);
        $activeModule = $modules->firstWhere('id', $selectedId) ?? $modules->first();

        return view('video.index', compact('modules', 'videoModules', 'activeModule'));
    }
}
