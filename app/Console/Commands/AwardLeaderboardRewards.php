<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\QuizResult;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserNotification;
use App\Helpers\WeekHelper;

class AwardLeaderboardRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'award:leaderboard {--week=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award vouchers to top leaderboard users (top 1..10) for a given week (default: this week)';

    public function handle(): int
    {
        $week = $this->option('week') ?: WeekHelper::thisWeekMonday();
        $this->info("Calculating leaderboard for week: {$week}");

        $rows = QuizResult::select('user_id', DB::raw('SUM(score) as total_score'))
            ->whereDate('week_start', $week)
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->get();

        if ($rows->isEmpty()) {
            $this->info('No quiz results for this week.');
            return 0;
        }

        $position = 0;
        foreach ($rows as $r) {
            $position++;
            if ($position > 10) break;

            $user = User::find($r->user_id);
            if (!$user) continue;
            if ($user->role !== 'user') continue;

            // Determine amount
            if ($position === 1) $amount = 100000;
            elseif ($position === 2) $amount = 50000;
            elseif ($position === 3) $amount = 25000;
            else $amount = 10000;

            // Avoid duplicate award for same week
            $exists = Reward::where('user_id', $user->id)
                ->where('type', 'leaderboard')
                ->whereDate('week_start', $week)
                ->exists();

            if ($exists) {
                $this->line("User {$user->id} already awarded for week {$week}, skipping.");
                continue;
            }

            $reward = Reward::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'leaderboard',
                'description' => "Leaderboard reward - position {$position}",
                'week_start' => $week,
                'awarded_at' => now(),
            ]);

            // Create in-app notification for the user
            $title = "Reward Leaderboard - Pos {$position}";
            $body  = "Selamat! Anda menerima voucher Rp" . number_format($amount, 0, ',', '.') . " untuk posisi ke-{$position} di leaderboard minggu ini.";
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'reward',
                'title' => $title,
                'body' => $body,
                'data' => json_encode(['reward_id' => $reward->id, 'position' => $position, 'amount' => $amount, 'week_start' => $week]),
            ]);

            $this->info("Awarded user {$user->id} (pos {$position}) amount {$amount}");
        }

        $this->info('Leaderboard awarding completed.');
        return 0;
    }
}
