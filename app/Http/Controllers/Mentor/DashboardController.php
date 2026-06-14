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

        // Hasil Quiz (dengan Search, Filter, Sort, Pagination)
        $resQ      = trim((string) request('res_q', ''));
        $resModule = (int) request('res_module', 0);
        $resSort   = request('res_sort', 'newest');
        
        $qrQuery = QuizResult::with(['user', 'module']);

        if ($resModule > 0) {
            $qrQuery->where('quiz_results.module_id', $resModule);
        }

        if ($resQ !== '') {
            $qrQuery->whereHas('user', function($q) use ($resQ) {
                $q->where('full_name', 'like', "%{$resQ}%")
                  ->orWhere('username', 'like', "%{$resQ}%")
                  ->orWhere('email', 'like', "%{$resQ}%");
            });
        }

        switch ($resSort) {
            case 'oldest':
                $qrQuery->orderBy('quiz_results.created_at', 'asc');
                break;
            case 'score_asc':
                $qrQuery->orderBy('quiz_results.score', 'asc')->orderBy('quiz_results.created_at', 'desc');
                break;
            case 'score_desc':
                $qrQuery->orderBy('quiz_results.score', 'desc')->orderBy('quiz_results.created_at', 'desc');
                break;
            case 'user_asc':
                $qrQuery->join('users', 'quiz_results.user_id', '=', 'users.id')
                        ->orderBy('users.username', 'asc')
                        ->orderBy('users.full_name', 'asc')
                        ->select('quiz_results.*');
                break;
            case 'user_desc':
                $qrQuery->join('users', 'quiz_results.user_id', '=', 'users.id')
                        ->orderBy('users.username', 'desc')
                        ->orderBy('users.full_name', 'desc')
                        ->select('quiz_results.*');
                break;
            case 'newest':
            default:
                $qrQuery->orderBy('quiz_results.created_at', 'desc');
                break;
        }

        $quizResults = $qrQuery->paginate(15)->withQueryString();

        return view('mentor.dashboard', compact(
            'tab', 'modules', 'selectedMod',
            'questions', 'editQ', 'editMod', 'quizResults',
        ));
    }
}
