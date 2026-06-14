<?php
namespace App\Http\Controllers;

use App\Helpers\WeekHelper;
use App\Models\Module;
use App\Models\ModuleCompletion;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuizResult;
use App\Models\Achievement;
use App\Http\Controllers\AchievementController as AchievementList;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $monday  = WeekHelper::thisWeekMonday();
        $modules = Module::orderBy('id')->get();

        $selectedId   = $request->integer('module', $modules->first()?->id);
        $activeModule = $modules->firstWhere('id', $selectedId) ?? $modules->first();

        $completedIds = ModuleCompletion::where('user_id', $userId)
            ->whereDate('week_start', $monday)
            ->pluck('module_id')
            ->map(fn($id) => (int)$id)
            ->toArray();

        $hasCompleted = in_array((int)$activeModule->id, $completedIds);

        $quizResult = QuizResult::where('user_id', $userId)
            ->where('module_id', $activeModule->id)
            ->whereDate('week_start', $monday)
            ->first();

        $hasQuiz    = (bool)$quizResult;
        $reviewMode = $hasQuiz && $request->has('review');

        // Review: tampilkan SEMUA soal yang pernah di-load (dari session atau dari answers_json)
        $reviewQuestions = collect();
        if ($hasQuiz && $reviewMode && $quizResult) {
            $savedAnswers = $quizResult->answers_json ?? [];
            $sessionKey   = "quiz_{$userId}_{$activeModule->id}_questions";

            // Coba ambil dari session (jika masih ada)
            $sessionQIds = session($sessionKey, []);

            if (!empty($sessionQIds)) {
                $reviewQuestions = Question::whereIn('id', $sessionQIds)->with('options')->get();
            } elseif (!empty($savedAnswers)) {
                // Fallback: ambil dari answered questions
                $reviewQuestions = Question::whereIn('id', array_keys($savedAnswers))->with('options')->get();
            }
        }

        // Load soal untuk quiz aktif
        $sessionKey = "quiz_{$userId}_{$activeModule->id}";
        if ($hasCompleted && !$hasQuiz && !session()->has($sessionKey)) {
            $questions = Question::where('module_id', $activeModule->id)
                ->with(['options' => fn($q) => $q->inRandomOrder()])
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->toArray();

            session([$sessionKey => $questions]);

            // Simpan juga ID soal untuk keperluan review
            $qIds = array_column($questions, 'id');
            session(["quiz_{$userId}_{$activeModule->id}_questions" => $qIds]);
        }

        $questions = ($hasCompleted && session()->has($sessionKey))
            ? session($sessionKey)
            : [];

        return view('quiz.index', compact(
            'modules', 'activeModule', 'completedIds',
            'hasCompleted', 'hasQuiz', 'quizResult',
            'reviewMode', 'reviewQuestions', 'questions'
        ));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'module_id'     => 'required|exists:modules,id',
            'answers'       => 'required|array',
            'total_questions'=> 'nullable|integer|min:1',
        ]);

        $userId = auth()->id();
        $monday = WeekHelper::thisWeekMonday();

        $existing = QuizResult::where('user_id', $userId)
            ->where('module_id', $validated['module_id'])
            ->whereDate('week_start', $monday)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz sudah dikerjakan minggu ini.',
            ]);
        }

        $answers       = $validated['answers'];
        $totalSoal     = $validated['total_questions'] ?? 10; // selalu /10 (atau jumlah soal yang di-load)
        $correct       = 0;

        foreach ($answers as $questionId => $optionId) {
            $isCorrect = Option::where('id', $optionId)
                ->where('question_id', $questionId)
                ->where('is_correct', 1)
                ->exists();
            if ($isCorrect) $correct++;
        }

        // Penilaian SELALU dibagi totalSoal (10), bukan jumlah yang terjawab
        $score = round(($correct / $totalSoal) * 100);

        // Simpan juga soal IDs di session untuk review nanti
        $sessionQKey = "quiz_{$userId}_{$validated['module_id']}_questions";
        $qIds        = array_keys($answers);
        session([$sessionQKey => $qIds]);

        // Load semua soal yang di-session agar review bisa tampil semua
        $sessionKey = "quiz_{$userId}_{$validated['module_id']}";
        if (session()->has($sessionKey)) {
            $allQIds = array_column(session($sessionKey), 'id');
            session([$sessionQKey => $allQIds]);
        }

        QuizResult::create([
            'user_id'      => $userId,
            'module_id'    => $validated['module_id'],
            'score'        => $score,
            'answers_json' => $answers,
            'week_start'   => $monday,
        ]);

        session()->forget("quiz_{$userId}_{$validated['module_id']}");

        // Check achievement conditions after quiz
        $awarded = [];
        $allAchievements = AchievementList::allAchievements();

        // Quiz Champion: skor 100
        if ($score === 100) {
            $key = 'Quiz Champion';
            $exists = Achievement::where('user_id', $userId)
                ->where('achievement_name', $key)
                ->exists();
            if (!$exists) {
                $icon = null;
                foreach ($allAchievements as $a) { if (strtolower($a['key']) === strtolower($key)) { $icon = $a['icon']; break; } }
                Achievement::create([
                    'user_id' => $userId,
                    'achievement_name' => $key,
                    'badge_icon' => $icon,
                    'date_earned' => now(),
                ]);
                $awarded[] = ['name' => $key, 'icon' => $icon];
            }
        }

        // Active Learner: kerjakan 5 quiz
        $totalQuizzes = QuizResult::where('user_id', $userId)->count();
        if ($totalQuizzes >= 5) {
            $key = 'Active Learner';
            $exists = Achievement::where('user_id', $userId)
                ->where('achievement_name', $key)
                ->exists();
            if (!$exists) {
                $icon = null;
                foreach ($allAchievements as $a) { if (strtolower($a['key']) === strtolower($key)) { $icon = $a['icon']; break; } }
                Achievement::create([
                    'user_id' => $userId,
                    'achievement_name' => $key,
                    'badge_icon' => $icon,
                    'date_earned' => now(),
                ]);
                $awarded[] = ['name' => $key, 'icon' => $icon];
            }
        }

        return response()->json(['success' => true, 'score' => $score, 'correct' => $correct, 'total' => $totalSoal, 'awarded' => $awarded]);
    }
}
