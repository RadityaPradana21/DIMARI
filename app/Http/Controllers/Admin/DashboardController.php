<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuizResult;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers     = User::where('role', 'user')->count();
        $totalModules   = Module::count();
        $totalQuestions = Question::count();
        $avgScore       = round(QuizResult::avg('score') ?? 0);

        $weekStart         = now()->startOfWeek();
        $totalQuizThisWeek = QuizResult::where('created_at', '>=', $weekStart)->count();

        $users    = User::latest()->get();
        $modules  = Module::withCount('questions')->latest()->get();
        $questions = Question::with(['module', 'options'])->latest()->get();
        $quizResults = QuizResult::with(['user', 'module'])->latest()->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalModules', 'totalQuestions',
            'totalQuizThisWeek', 'avgScore',
            'users', 'modules', 'questions', 'quizResults',
        ));
    }
}
