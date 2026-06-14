<?php

namespace App\Providers;

use App\Models\DiscussionForum;
use App\Models\ForumReply;
use App\Policies\DiscussionForumPolicy;
use App\Policies\ForumReplyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        DiscussionForum::class => DiscussionForumPolicy::class,
        ForumReply::class => ForumReplyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
