<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleCompletion extends Model
{
    public $timestamps = false; // tabel hanya punya completed_at

    protected $fillable = ['user_id', 'module_id', 'week_start'];

    // PENTING: cast ke 'date' agar konsisten dengan Y-m-d string dari WeekHelper
    protected $casts = [
        'week_start' => 'date:Y-m-d',
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
