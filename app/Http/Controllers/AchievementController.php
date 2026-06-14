<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Support\Str;

class AchievementController extends Controller
{
    public static function allAchievements(): array
    {
        return [
            ['key' => 'First Module',      'icon' => '📚', 'name' => 'First Module',      'desc' => 'Selesaikan modul pertamamu'],
            ['key' => 'Learning Master',   'icon' => '🏆', 'name' => 'Learning Master',   'desc' => 'Selesaikan semua modul dalam seminggu'],
            ['key' => 'Dedicated Learner', 'icon' => '🥇', 'name' => 'Dedicated Learner', 'desc' => 'Login 7 hari berturut-turut'],
            ['key' => 'Active Learner',    'icon' => '🥈', 'name' => 'Active Learner',    'desc' => 'Kerjakan 5 quiz'],
            ['key' => 'Quiz Champion',     'icon' => '🎯', 'name' => 'Quiz Champion',     'desc' => 'Raih skor 100 pada sebuah quiz'],
            ['key' => 'Perfect Week',      'icon' => '⭐', 'name' => 'Perfect Week',      'desc' => 'Raih skor sempurna seminggu'],
            ['key' => 'Forum Participant', 'icon' => '💬', 'name' => 'Forum Participant', 'desc' => 'Posting topik di forum diskusi'],
            ['key' => 'Hot Streak',        'icon' => '🔥', 'name' => 'Hot Streak',        'desc' => 'Aktif 3 minggu berturut-turut'],
            ['key' => 'Top Scorer',        'icon' => '🎖',  'name' => 'Top Scorer',        'desc' => 'Masuk top 3 leaderboard mingguan'],
            ['key' => 'Fast Learner',      'icon' => '⚡', 'name' => 'Fast Learner',      'desc' => 'Selesaikan modul dalam 5 menit'],
        ];
    }

    public function index()
    {
        $userId          = auth()->id();
        $allAchievements = self::allAchievements();

        // DB menyimpan achievement_name sebagai string — ambil semua achievement user
        $earned = Achievement::where('user_id', $userId)->get();

        // Tambahkan slug ke daftar semua achievement agar matching lebih stabil
        foreach ($allAchievements as &$a) {
            $a['slug'] = Str::slug($a['key']);
        }

        // Build map: gunakan slug untuk mencocokkan nama achievement DB ke definisi
        $earnedByKey = [];
        foreach ($earned as $e) {
            $slug = Str::slug($e->achievement_name);
            foreach ($allAchievements as $ach) {
                if ($slug === $ach['slug']) {
                    // simpan model Achievement per key definisi
                    $earnedByKey[$ach['key']] = $e;
                    break;
                }
            }
        }

        $unlockedCount = count($earnedByKey);

        return view('achievements.index', compact(
            'allAchievements', 'earnedByKey', 'unlockedCount'
        ));
    }
}
