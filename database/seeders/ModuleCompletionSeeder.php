<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleCompletion;

class ModuleCompletionSeeder extends Seeder
{
    public function run(): void
    {
        ModuleCompletion::insert([

            [
                'id' => 1,
                'user_id' => 3,
                'module_id' => 1,
                'week_start' => '2026-05-04 00:00:00',
                'completed_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 2,
                'user_id' => 4,
                'module_id' => 2,
                'week_start' => '2026-05-04 00:00:00',
                'completed_at' => '2026-05-09 12:01:39',
            ],

            [
                'id' => 3,
                'user_id' => 4,
                'module_id' => 7,
                'week_start' => '2026-05-04 00:00:00',
                'completed_at' => '2026-05-09 13:14:52',
            ],

        ]);
    }
}