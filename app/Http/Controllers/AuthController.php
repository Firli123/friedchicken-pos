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
        // Logout user lama dulu jika ada
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

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        ActivityLog::log('login', 'User ' . Auth::user()->name . ' berhasil login');

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