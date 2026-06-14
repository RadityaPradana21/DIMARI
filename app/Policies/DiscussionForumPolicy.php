<?php

namespace App\Policies;

use App\Models\DiscussionForum;
use App\Models\User;

class DiscussionForumPolicy
{
    /**
     * Determine whether the user can view the forum.
     */
    public function view(User $user, DiscussionForum $forum): bool
    {
        return true; // All authenticated users can view
    }

    /**
     * Determine whether the user can create forums.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['user', 'mentor', 'admin']);
    }

    /**
     * Determine whether the user can update the forum.
     */
    public function update(User $user, DiscussionForum $forum): bool
    {
        // Users can only edit their own discussions
        if ($user->role === 'user' && $forum->author_id === $user->id) {
            return true;
        }
        
        // Mentors can only edit their own discussions
        if ($user->role === 'mentor' && $forum->author_id === $user->id) {
            return true;
        }
        
        // Admins can edit any discussion
        if ($user->role === 'admin') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the forum.
     */
    public function delete(User $user, DiscussionForum $forum): bool
    {
        // Only admins can delete discussions
        return $user->role === 'admin';
    }
}
