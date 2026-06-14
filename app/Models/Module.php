<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['title', 'description', 'content', 'video_title', 'video_description', 'video_url'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function completions()
    {
        return $this->hasMany(ModuleCompletion::class);
    }

    public function quizResults()
    {
        return $this->hasMany(QuizResult::class);
    }
}