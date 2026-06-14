<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
                'full_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            DB::beginTransaction();

            try {
                // Create user first
                $user = User::create([
                    'username' => $validated['username'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'user',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                report($e);
                return back()->withInput($request->except('password', 'password_confirmation'))
                             ->withErrors(['register' => 'Gagal membuat akun. Coba lagi atau hubungi admin.']);
            }

            event(new Registered($user));

            Auth::login($user);

            return redirect(RouteServiceProvider::HOME)->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }
}
