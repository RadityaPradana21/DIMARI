<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;

use Illuminate\Support\Facades\Log;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('login')
                ->with('status', __($status));
        }

        // If sending failed, log and show a friendly message
        Log::warning('Password reset link sending failed for: ' . $request->input('email'));

        return back()->withErrors(['email' => __($status)]);
    }
}
