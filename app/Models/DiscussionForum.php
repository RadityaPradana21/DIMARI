<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionForum extends Model
{
    protected $fillable = ['title', 'content', 'author_id', 'category', 'visible'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'forum_id');
    }
}