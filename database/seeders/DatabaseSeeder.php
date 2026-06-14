<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        UserSeeder::class,
        ModuleSeeder::class,
        UserAnalyticSeeder::class,
        QuestionSeeder::class,
        OptionSeeder::class,
        AchievementSeeder::class,
        DiscussionForumSeeder::class,
        ForumReplySeeder::class,
        ModuleCompletionSeeder::class,
        QuizResultSeeder::class,
        ]);
    }
}