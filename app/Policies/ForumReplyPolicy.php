<?php

namespace App\Policies;

use App\Models\ForumReply;
use App\Models\User;

class ForumReplyPolicy
{
    /**
     * Determine whether the user can view the reply.
     */
    public function view(User $user, ForumReply $reply): bool
    {
        return true; // All authenticated users can view
    }

    /**
     * Determine whether the user can create replies.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['user', 'mentor', 'admin']);
    }

    /**
     * Determine whether the user can update the reply.
     */
    public function update(User $user, ForumReply $reply): bool
    {
        // Users can only edit their own replies
        if ($user->role === 'user' && $reply->user_id === $user->id) {
            return true;
        }
        
        // Mentors can only edit their own replies
        if ($user->role === 'mentor' && $reply->user_id === $user->id) {
            return true;
        }
        
        // Admins can edit any reply
        if ($user->role === 'admin') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the reply.
     */
    public function delete(User $user, ForumReply $reply): bool
    {
        // Only admins can delete replies
        return $user->role === 'admin';
    }
}
