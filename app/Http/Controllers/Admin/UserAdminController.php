<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')->latest()->paginate(30);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'in:admin,editor,vendedor',
        ]);

        $user->roles()->delete();
        foreach ($request->input('roles', []) as $role) {
            UserRole::create(['user_id' => $user->id, 'role' => $role]);
        }

        return back()->with('success', 'Roles actualizados.');
    }
}
