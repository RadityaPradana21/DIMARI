<?php

namespace App\Http\Controllers;

use App\Models\DiscussionForum;
use App\Models\ForumReply;
use App\Models\Achievement;
use App\Http\Controllers\AchievementController as AchievementList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ForumController extends Controller
{
    public const CATEGORIES = [
        'General', 'SEO', 'Content', 'Social Media',
        'Email Marketing', 'Analytics', 'Community',
    ];

    public function index(Request $request)
    {
        // Multiple filter: ?cats[]=SEO&cats[]=Content
        $activeCats = $request->input('cats', []);
        // Jika tidak ada filter → tampilkan semua
        $query = DiscussionForum::with(['author', 'replies.user'])
            ->orderByDesc('created_at');

        if (!empty($activeCats)) {
            $query->whereIn('category', $activeCats);
        }

        $forums     = $query->paginate(15)->withQueryString();
        $categories = self::CATEGORIES;

        return view('forum.index', compact('forums', 'categories', 'activeCats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', DiscussionForum::class);

        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);

        DiscussionForum::create([
            'title'     => $request->title,
            'content'   => $request->content,
            'author_id' => auth()->id(),
            'category'  => $request->category ?? 'General',
        ]);

        // Award "Forum Participant" achievement if not yet owned
        $userId = auth()->id();
        $key = 'Forum Participant';
        $exists = Achievement::where('user_id', $userId)
            ->where('achievement_name', $key)
            ->exists();

        if (!$exists) {
            $allAchievements = AchievementList::allAchievements();
            $icon = null;
            foreach ($allAchievements as $a) { if (strtolower($a['key']) === strtolower($key)) { $icon = $a['icon']; break; } }
            Achievement::create([
                'user_id' => $userId,
                'achievement_name' => $key,
                'badge_icon' => $icon,
                'date_earned' => now(),
            ]);
            return redirect()->to(route('achievements') . '?open=' . urlencode($key))
                ->with('success', 'Topik berhasil dibuat!')
                ->with('awarded', [$key]);
        }

        return redirect()->route('forum')->with('success', 'Topik berhasil dibuat!');
    }

    public function reply(Request $request, DiscussionForum $forum)
    {
        Gate::authorize('create', ForumReply::class);

        $request->validate(['content' => 'required|string|max:2000']);

        ForumReply::create([
            'forum_id'        => $forum->id,
            'user_id'         => auth()->id(),
            'content'         => $request->content,
            'is_mentor_reply' => auth()->user()->role === 'mentor',
        ]);

        return redirect()->route('forum')->with('success', 'Balasan terkirim!');
    }

    public function edit(DiscussionForum $forum)
    {
        Gate::authorize('update', $forum);

        return view('forum.edit', compact('forum'));
    }

    public function update(Request $request, DiscussionForum $forum)
    {
        Gate::authorize('update', $forum);

        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);

        $forum->update([
            'title'    => $request->title,
            'content'  => $request->content,
            'category' => $request->category ?? 'General',
        ]);

        return redirect()->route('forum')->with('success', 'Topik berhasil diperbarui!');
    }

    public function destroy(DiscussionForum $forum)
    {
        Gate::authorize('delete', $forum);

        $forum->delete();

        return redirect()->route('forum')->with('success', 'Topik berhasil dihapus!');
    }

    public function editReply(ForumReply $reply)
    {
        Gate::authorize('update', $reply);

        return view('forum.edit-reply', compact('reply'));
    }

    public function updateReply(Request $request, ForumReply $reply)
    {
        Gate::authorize('update', $reply);

        $request->validate(['content' => 'required|string|max:2000']);

        $reply->update(['content' => $request->content]);

        return redirect()->route('forum')->with('success', 'Balasan berhasil diperbarui!');
    }

    public function destroyReply(ForumReply $reply)
    {
        Gate::authorize('delete', $reply);

        $reply->delete();

        return redirect()->route('forum')->with('success', 'Balasan berhasil dihapus!');
    }
}
