<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'achievement_name', 'badge_icon', 'date_earned',
    ];

    protected $casts = [
        'date_earned' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}