<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = ['user_id', 'module_id', 'score', 'answers_json', 'week_start'];

    protected $casts = [
        'week_start'   => 'date:Y-m-d',
        'answers_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
