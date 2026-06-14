<?php

namespace App\Http\Controllers;

use App\Helpers\WeekHelper;
use App\Models\Module;
use App\Models\ModuleCompletion;
use App\Models\QuizResult;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $monday = WeekHelper::thisWeekMonday();

        $modules      = Module::orderBy('id')->get();
        $totalModules = $modules->count();

        $doneIds = ModuleCompletion::where('user_id', $userId)
            ->whereDate('week_start', $monday)
            ->pluck('module_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $completedCount = count($doneIds);

        $weekScore = QuizResult::where('user_id', $userId)
            ->whereDate('week_start', $monday)
            ->sum('score');

        $raw = QuizResult::select('user_id', DB::raw('SUM(score) as total_score'))
            ->with('user')
            ->whereDate('week_start', $monday)
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->take(10)
            ->get();

        $pos = 0;
        $leaderboard = $raw->map(function($item) use (&$pos) {
            $pos++;
            if ($pos === 1) $amount = 100000;
            elseif ($pos === 2) $amount = 50000;
            elseif ($pos === 3) $amount = 25000;
            else $amount = 10000;

            return (object) [
                'user_id' => $item->user_id,
                'username' => $item->user->username ?? '-',
                'total_score' => $item->total_score,
                'rank' => $pos,
                'reward_amount' => $amount,
                'reward_label' => 'Voucher Rp' . number_format($amount, 0, ',', '.'),
            ];
        });

        // Fetch unread notifications for current user (and mark as read)
        $notifications = UserNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->get();

        if ($notifications->isNotEmpty()) {
            foreach ($notifications as $n) {
                $n->read_at = now();
                $n->save();
            }
        }

        return view('dashboard', compact(
            'modules', 'totalModules', 'completedCount', 'doneIds',
            'weekScore', 'leaderboard', 'notifications'
        ));
    }
}
