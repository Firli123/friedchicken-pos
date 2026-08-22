<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isOwner()
                ? redirect()->route('dashboard')
                : redirect()->route('pos.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Logout user lama jika masih ada session
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            'username'  => $request->username,
            'password'  => $request->password,
            'is_active' => true,
        ];

        // Remember me: true = session 8 jam, false = session normal
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        // Set lifetime session berdasarkan remember
        if ($remember) {
            config(['session.lifetime' => 480]); // 8 jam
        }

        ActivityLog::log(
            'login',
            'User ' . Auth::user()->name . ' berhasil login' . ($remember ? ' (ingat saya)' : '')
        );

        return Auth::user()->isOwner()
            ? redirect()->route('dashboard')
            : redirect()->route('pos.index');
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'User ' . Auth::user()->name . ' logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}