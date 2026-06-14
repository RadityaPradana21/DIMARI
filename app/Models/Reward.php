<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'type', 'description', 'week_start', 'awarded_at'
    ];

    protected $casts = [
        'week_start' => 'date',
        'awarded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
