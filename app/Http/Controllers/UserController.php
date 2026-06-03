<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('name');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff',
        ]);

        // Username is required for admins
        if ($validated['role'] === 'admin' && empty($validated['username'])) {
            return back()->withErrors(['username' => 'Username is required for admin accounts.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        ActivityLogger::created($user);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id . '|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,staff',
        ]);

        // Username is required for admins
        if ($validated['role'] === 'admin' && empty($validated['username'])) {
            return back()->withErrors(['username' => 'Username is required for admin accounts.'])->withInput();
        }

        $original = $user->getOriginal();
        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        ActivityLogger::updated($user, $original);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
            ActivityLogger::deleted($user);
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user. Please try again.');
        }

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
