<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::insert([

            [
                'id' => 1,
                'user_id' => 2,
                'achievement_name' => 'Learning Master',
                'badge_icon' => '🏆',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 2,
                'user_id' => 2,
                'achievement_name' => 'Dedicated Learner',
                'badge_icon' => '🥇',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 3,
                'user_id' => 3,
                'achievement_name' => 'Dedicated Learner',
                'badge_icon' => '🥇',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 4,
                'user_id' => 3,
                'achievement_name' => 'Active Learner',
                'badge_icon' => '🥈',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 5,
                'user_id' => 3,
                'achievement_name' => 'Forum Participant',
                'badge_icon' => '💬',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 6,
                'user_id' => 4,
                'achievement_name' => 'Active Learner',
                'badge_icon' => '🥈',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 7,
                'user_id' => 4,
                'achievement_name' => 'Quiz Champion',
                'badge_icon' => '🎯',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 8,
                'user_id' => 5,
                'achievement_name' => 'Learning Master',
                'badge_icon' => '🏆',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 9,
                'user_id' => 5,
                'achievement_name' => 'Forum Participant',
                'badge_icon' => '💬',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 10,
                'user_id' => 6,
                'achievement_name' => 'Learning Master',
                'badge_icon' => '🏆',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 11,
                'user_id' => 6,
                'achievement_name' => 'Quiz Champion',
                'badge_icon' => '🎯',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 12,
                'user_id' => 6,
                'achievement_name' => 'Forum Participant',
                'badge_icon' => '💬',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 13,
                'user_id' => 7,
                'achievement_name' => 'Dedicated Learner',
                'badge_icon' => '🥇',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 14,
                'user_id' => 7,
                'achievement_name' => 'Active Learner',
                'badge_icon' => '🥈',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 15,
                'user_id' => 8,
                'achievement_name' => 'Dedicated Learner',
                'badge_icon' => '🥇',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 16,
                'user_id' => 8,
                'achievement_name' => 'Forum Participant',
                'badge_icon' => '💬',
                'date_earned' => '2026-05-09 11:43:09',
            ],

            [
                'id' => 17,
                'user_id' => 4,
                'achievement_name' => 'Module Completed',
                'badge_icon' => '📚',
                'date_earned' => '2026-05-09 12:01:39',
            ],

        ]);
    }
}