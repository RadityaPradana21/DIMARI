<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuizManagementController extends Controller
{
    // List semua quiz/question (dengan pencarian, filter module, pagination)
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $moduleId = $request->query('module');
        $perPage = (int) $request->query('per_page', 15);

        $query = Question::with(['module', 'options']);

        if (!empty($moduleId)) {
            $query->where('module_id', (int) $moduleId);
        }

        if ($q !== '') {
            $query->where(function ($wr) use ($q) {
                $wr->where('question_text', 'like', "%{$q}%")
                   ->orWhereHas('options', function ($o) use ($q) {
                        $o->where('option_text', 'like', "%{$q}%");
                   })
                   ->orWhereHas('module', function ($m) use ($q) {
                        $m->where('title', 'like', "%{$q}%");
                   });
            });
        }

        $questions = $query->orderByDesc('created_at')
                           ->paginate(max(5, min(200, $perPage)))
                           ->withQueryString();

        $modules = Module::orderBy('title')->get();

        return view('admin.quizzes.index', compact('questions', 'modules', 'q', 'moduleId'));
    }

    // Form tambah quiz
    public function create()
    {
        $modules = Module::orderBy('title')->get();

        return view('admin.quizzes.create', compact('modules'));
    }

    // Simpan quiz baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer',
        ]);
        $question = Question::create([
            'module_id'    => $validated['module_id'],
            'question_text'=> $validated['question_text'],
        ]);
        foreach ($validated['options'] as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => (
                    $index == $validated['correct_option']
                ),
            ]);
        }
        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil ditambahkan.');
    }

    // Detail quiz
    public function show(Question $quiz)
    {
        $quiz->load(['module', 'options']);
        return view('admin.quizzes.show', ['question' => $quiz]);
    }

    // Form edit quiz
    public function edit(Question $quiz)
    {
        $quiz->load('options');
        $modules = Module::orderBy('title')->get();
        return view('admin.quizzes.edit', [
            'question' => $quiz,
            'modules'  => $modules,
        ]);
    }

    // Update quiz
    public function update(Request $request, Question $quiz)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer',
        ]);
        $quiz->update([
            'module_id' => $validated['module_id'],
            'question_text' => $validated['question_text'],
        ]);
        $quiz->options()->delete();
        foreach ($validated['options'] as $index => $optionText) {
            Option::create([
                'question_id' => $quiz->id,
                'option_text' => $optionText,
                'is_correct' => (
                    $index == $validated['correct_option']
                ),
            ]);
        }
        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil diperbarui.');
    }

    // Hapus quiz
    public function destroy(Question $quiz)
    {
        $quiz->options()->delete();
        $quiz->delete();
        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil dihapus.');
    }
}