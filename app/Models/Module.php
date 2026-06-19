<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;
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