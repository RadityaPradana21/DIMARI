<?php
namespace App\Http\Controllers;

use App\Models\UserAnalytic;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    // Pengganti api/track_interaction.php
    public function trackInteraction(Request $request)
    {
        $analytic = UserAnalytic::firstOrCreate(
            ['user_id' => auth()->id()]
        );
        $analytic->increment('page_visits');
        return response()->json(['success' => true]);
    }

    // Pengganti api/track_page_visit.php
    public function trackPageVisit(Request $request)
    {
        UserAnalytic::updateOrCreate(
            ['user_id' => auth()->id()],
            ['last_login' => now()]
        );
        return response()->json(['success' => true]);
    }

    // Pengganti api/update_learning_time.php
    public function updateLearningTime(Request $request)
    {
        $validated = $request->validate([
            'seconds' => 'required|integer|min:1',
        ]);

        $analytic = UserAnalytic::firstOrCreate(
            ['user_id' => auth()->id()]
        );
        $analytic->increment('total_learning_time', $validated['seconds']);

        return response()->json(['success' => true]);
    }
}