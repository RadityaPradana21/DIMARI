<?php

namespace App\Http\Controllers;

use App\Helpers\ContentRenderer;
use App\Helpers\WeekHelper;
use App\Models\Module;
use App\Models\ModuleCompletion;
use App\Models\Achievement;
use App\Http\Controllers\AchievementController as AchievementList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MateriController extends Controller
{
    private array $defaultIcons = [
        1 => '📱',
        2 => '🎯',
        3 => '📊',
        4 => '🔍',
        5 => '🎬',
        6 => '📧',
        7 => '💡',
        8 => '🌐',
        9 => '📈',
        10 => '🤝',
    ];

    public function index(Request $request)
    {
        $userId  = auth()->id();
        $monday  = WeekHelper::thisWeekMonday();
        $modules = Module::orderBy('id')->get();

        $doneIds = ModuleCompletion::where('user_id', $userId)
            ->whereDate('week_start', $monday)
            ->pluck('module_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $activeId     = $request->integer('id', $modules->first()?->id);
        $activeModule = $modules->firstWhere('id', $activeId) ?? $modules->first();
        $alreadyDone  = in_array((int) $activeModule->id, $doneIds);

        $moduleContent = ContentRenderer::toHtml($activeModule->content ?? '');

        $moduleIcons = $this->defaultIcons;   // ← jadikan variable lokal dulu

        return view('materi.index', compact(
            'modules',
            'activeModule',
            'doneIds',
            'alreadyDone',
            'moduleIcons',
            'moduleContent'    // ← pakai $moduleIcons yang sudah terdefinisi
        ));
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
        ]);

        $userId = auth()->id();
        $monday = WeekHelper::thisWeekMonday();

        $alreadyDone = ModuleCompletion::where('user_id', $userId)
            ->where('module_id', $validated['module_id'])
            ->whereDate('week_start', $monday)
            ->exists();

        if ($alreadyDone) {
            return response()->json(['success' => false, 'message' => 'Sudah diselesaikan minggu ini.']);
        }

        try {
            // Count total completed modules before inserting (to detect "First Module")
            $prevTotalCompleted = ModuleCompletion::where('user_id', $userId)->count();

            ModuleCompletion::create([
                'user_id'    => $userId,
                'module_id'  => (int) $validated['module_id'],
                'week_start' => $monday,
            ]);

            // After create: check achievement conditions
            $awarded = [];
            $allAchievements = AchievementList::allAchievements();

            // 1) First Module — jika sebelumnya belum ada completion
            if ($prevTotalCompleted === 0) {
                $key = 'First Module';
                $exists = Achievement::where('user_id', $userId)
                    ->where('achievement_name', $key)
                    ->exists();
                if (!$exists) {
                    $icon = null;
                    foreach ($allAchievements as $a) {
                        if (strtolower($a['key']) === strtolower($key)) { $icon = $a['icon']; break; }
                    }
                    Achievement::create([
                        'user_id' => $userId,
                        'achievement_name' => $key,
                        'badge_icon' => $icon,
                        'date_earned' => now(),
                    ]);
                    $awarded[] = ['name' => $key, 'icon' => $icon];
                }
            }

            // 2) Learning Master — selesaikan semua modul di minggu yang sama
            $weekCompleted = ModuleCompletion::where('user_id', $userId)
                ->whereDate('week_start', $monday)
                ->count();
            $totalModules = Module::count();
            if ($totalModules > 0 && $weekCompleted >= $totalModules) {
                $key = 'Learning Master';
                $exists = Achievement::where('user_id', $userId)
                    ->where('achievement_name', $key)
                    ->exists();
                if (!$exists) {
                    $icon = null;
                    foreach ($allAchievements as $a) {
                        if (strtolower($a['key']) === strtolower($key)) { $icon = $a['icon']; break; }
                    }
                    Achievement::create([
                        'user_id' => $userId,
                        'achievement_name' => $key,
                        'badge_icon' => $icon,
                        'date_earned' => now(),
                    ]);
                    $awarded[] = ['name' => $key, 'icon' => $icon];
                }
            }

            return response()->json(['success' => true, 'awarded' => $awarded]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['success' => true]);
            }
            Log::error('ModuleCompletion DB error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan.'], 500);
        } catch (\Exception $e) {
            Log::error('ModuleCompletion error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan.'], 500);
        }
    }
}
