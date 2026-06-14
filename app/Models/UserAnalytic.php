<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnalytic extends Model
{
    protected $fillable = [
        'user_id', 'last_login', 'total_learning_time',
        'page_visits', 'last_accessed_module',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}