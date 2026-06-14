<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAnalytic;

class UserAnalyticSeeder extends Seeder
{
    public function run(): void
    {
        UserAnalytic::insert([

            [
                'user_id' => 1,
                'total_learning_time' => 0,
                'page_visits' => 0,
                'last_module_id' => 3,
            ],

            [
                'user_id' => 2,
                'total_learning_time' => 0,
                'page_visits' => 0,
                'last_module_id' => 2,
            ],

            [
                'user_id' => 3,
                'total_learning_time' => 0,
                'page_visits' => 0,
                'last_module_id' => 2,
            ],

            [
                'user_id' => 4,
                'total_learning_time' => 45,
                'page_visits' => 0,
                'last_module_id' => 7,
            ],

            [
                'user_id' => 5,
                'total_learning_time' => 265,
                'page_visits' => 43,
                'last_module_id' => 1,
            ],

            [
                'user_id' => 6,
                'total_learning_time' => 211,
                'page_visits' => 33,
                'last_module_id' => 3,
            ],

            [
                'user_id' => 7,
                'total_learning_time' => 123,
                'page_visits' => 24,
                'last_module_id' => 1,
            ],

            [
                'user_id' => 8,
                'total_learning_time' => 125,
                'page_visits' => 26,
                'last_module_id' => 3,
            ],

        ]);
    }
}