<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\QuizResult;
use App\Models\ModuleCompletion;
use App\Helpers\WeekHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user          = $request->user();
        $monday        = WeekHelper::thisWeekMonday();
        $totalScore    = QuizResult::where('user_id', $user->id)->sum('score');
        $weekScore     = QuizResult::where('user_id', $user->id)->whereDate('week_start', $monday)->sum('score');
        $totalCompleted = ModuleCompletion::where('user_id', $user->id)->count();
        $quizHistory   = QuizResult::where('user_id', $user->id)->with('module')->orderByDesc('created_at')->take(10)->get();
        $achievements  = Achievement::where('user_id', $user->id)->orderByDesc('date_earned')->get();
        $unlockedCount = $achievements->count();
        $avgScore      = round(QuizResult::where('user_id', $user->id)->avg('score') ?? 0);

        return view('profile.index', compact(
            'user', 'totalScore', 'weekScore', 'totalCompleted',
            'quizHistory', 'achievements', 'unlockedCount', 'avgScore'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->user()->update($request->validate([
            'full_name'    => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]));
        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'min:6', 'confirmed'],
        ]);
        $request->user()->update(['password' => Hash::make($validated['password'])]);
        return Redirect::route('profile')->with('status', 'password-updated');
    }

    /**
     * Pilih avatar default (nomor 1–10).
     * Simpan sebagai full URL persis seperti DIMARI2 agar kompatibel.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar_choice' => 'required|integer|min:1|max:10',
        ]);

        $num = $validated['avatar_choice'];

        // Simpan sebagai full URL — konsisten dengan DIMARI2
        $avatarUrl = asset('avatars/default/avatar_' . $num . '.svg');

        $request->user()->update([
            'avatar_url'             => $avatarUrl,
            'is_using_custom_avatar' => false,
        ]);

        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    /**
     * Upload avatar custom.
     * Simpan ke storage/app/public/avatars/custom/, catat path relatifnya.
     */
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'custom_avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file     = $request->file('custom_avatar');
        $userId   = auth()->id();
        $filename = 'user_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Simpan ke storage/app/public/avatars/custom/
        $file->storeAs('avatars/custom', $filename, 'public');

        $request->user()->update([
            // Simpan path relatif (storage link akan resolve ke public/storage/avatars/custom/)
            'avatar_url'             => 'avatars/custom/' . $filename,
            'is_using_custom_avatar' => true,
        ]);

        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    public function resetAvatar(Request $request): RedirectResponse
    {
        $request->user()->update([
            'avatar_url'             => null,
            'is_using_custom_avatar' => false,
        ]);
        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', ['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}
