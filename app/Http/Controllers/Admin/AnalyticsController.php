<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAnalytic;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Statistik umum
        $totalUsers = User::count();

        $totalVisits = UserAnalytic::sum('page_visits');

        $totalLearningTime = UserAnalytic::sum(
            'total_learning_time'
        );

        // Data analytics user
        $analytics = UserAnalytic::with('user')
            ->latest()
            ->get();

        return view(
            'admin.analytics.index',
            compact(
                'totalUsers',
                'totalVisits',
                'totalLearningTime',
                'analytics'
            )
        );
    }
}