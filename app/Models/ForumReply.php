<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $fillable = [
        'forum_id', 'user_id', 'content',
        'parent_reply_id', 'is_mentor_reply', 'likes_count',
    ];

    protected $casts = [
        'is_mentor_reply' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forum()
    {
        return $this->belongsTo(DiscussionForum::class, 'forum_id');
    }

    public function parentReply()
    {
        return $this->belongsTo(ForumReply::class, 'parent_reply_id');
    }

    public function childReplies()
    {
        return $this->hasMany(ForumReply::class, 'parent_reply_id');
    }
}