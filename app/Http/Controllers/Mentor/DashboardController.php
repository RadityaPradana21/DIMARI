<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuizResult;

class DashboardController extends Controller
{
    public function index()
    {
        $tab     = request('tab', 'questions');
        $searchQ = trim((string) request('q', ''));
        $perPage = (int) request('per_page', 15);

        $modules = Module::orderBy('id')->get();

        $selectedMod = (int) request('module', $modules->first()?->id ?? 0);

        $questionsQuery = Question::with('options');

        if ($selectedMod) {
            $questionsQuery->where('module_id', $selectedMod);
        }

        if ($searchQ !== '') {
            $questionsQuery->where(function ($wr) use ($searchQ) {
                $wr->where('question_text', 'like', "%{$searchQ}%")
                   ->orWhereHas('options', function ($o) use ($searchQ) {
                       $o->where('option_text', 'like', "%{$searchQ}%");
                   })
                   ->orWhereHas('module', function ($m) use ($searchQ) {
                       $m->where('title', 'like', "%{$searchQ}%");
                   });
            });
        }

        $questions = $questionsQuery->orderBy('id')
                                    ->paginate(max(5, min(200, $perPage)))
                                    ->withQueryString();

        // Edit soal
        $editQ = null;
        if (request()->has('edit_q')) {
            $editQ = Question::with('options')->find((int) request('edit_q'));
        }

        // Edit modul (inline — seperti edit quiz)
        $editMod = null;
        if (request()->has('edit_mod')) {
            $editMod = Module::find((int) request('edit_mod'));
        }

        $quizResults = QuizResult::with(['user', 'module'])->latest()->get();

        return view('mentor.dashboard', compact(
            'tab', 'modules', 'selectedMod',
            'questions', 'editQ', 'editMod', 'quizResults',
        ));
    }
}
