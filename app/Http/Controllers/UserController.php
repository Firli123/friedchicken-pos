<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', 'unique:users', 'alpha_dash'],
            'email'     => ['nullable', 'email', 'unique:users'],
            'password'  => ['required', 'min:6', 'confirmed'],
            'role'      => ['required', 'in:owner,kasir'],
            'is_active' => ['boolean'],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        ActivityLog::log('create', "User '{$user->name}' ({$user->role}) ditambahkan", 'User', $user->id);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email'     => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'password'  => ['nullable', 'min:6', 'confirmed'],
            'role'      => ['required', 'in:owner,kasir'],
            'is_active' => ['boolean'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $user->update($data);

        ActivityLog::log('update', "User '{$user->name}' diperbarui", 'User', $user->id);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('delete', "User '{$name}' dihapus", 'User', $user->id);

        return redirect()->route('users.index')
            ->with('success', "User '{$name}' berhasil dihapus.");
    }
}
