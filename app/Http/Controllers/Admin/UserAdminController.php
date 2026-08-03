<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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
        // Defensa en profundidad: esta ruta ya está detrás de `role:admin`,
        // pero si alguna vez se relaja ese middleware, esto sigue evitando
        // que un usuario no-admin se autoasigne (o le asigne a otro) el rol admin.
        abort_unless($request->user()->isAdmin(), 403);

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

    /**
     * Dispara el mismo flujo de "olvidé mi contraseña" que usaría el propio
     * usuario, pero iniciado por un Admin desde el panel (ej. el usuario
     * llamó pidiendo ayuda para entrar).
     */
    public function sendPasswordReset(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $status = Password::sendResetLink(['email' => $user->email]);

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'success' : 'error',
            $status === Password::RESET_LINK_SENT
                ? "Enviamos un enlace de restablecimiento de contraseña a {$user->email}."
                : 'No pudimos enviar el enlace: ' . __($status)
        );
    }
}
