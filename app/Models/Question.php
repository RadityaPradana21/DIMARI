<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['module_id', 'question_text'];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}