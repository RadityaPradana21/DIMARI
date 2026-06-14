<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuizManagementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'module_id'      => 'required|exists:modules,id',
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2',
            'options.*'      => 'required|string',
            'correct_option' => 'required|integer',
        ]);

        $question = Question::create([
            'module_id'     => $request->module_id,
            'question_text' => $request->question_text,
        ]);

        foreach ($request->options as $i => $text) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct'  => ($i == $request->correct_option),
            ]);
        }

        return redirect()
            ->route('mentor.index', ['tab' => 'questions', 'module' => $request->module_id])
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function update(Request $request, Question $quiz)
    {
        $request->validate([
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2',
            'options.*'      => 'required|string',
            'correct_option' => 'required|integer',
        ]);

        $quiz->update(['question_text' => $request->question_text]);

        $quiz->options()->delete();
        foreach ($request->options as $i => $text) {
            Option::create([
                'question_id' => $quiz->id,
                'option_text' => $text,
                'is_correct'  => ($i == $request->correct_option),
            ]);
        }

        return redirect()
            ->route('mentor.index', ['tab' => 'questions', 'module' => $quiz->module_id])
            ->with('success', 'Soal berhasil diupdate!');
    }

    public function destroy(Question $quiz)
    {
        $moduleId = $quiz->module_id;
        $quiz->options()->delete();
        $quiz->delete();

        return redirect()
            ->route('mentor.index', ['tab' => 'questions', 'module' => $moduleId])
            ->with('success', 'Soal dihapus.');
    }
}