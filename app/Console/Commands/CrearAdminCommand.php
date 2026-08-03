<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CrearAdminCommand extends Command
{
    protected $signature = 'app:crear-admin';

    protected $description = 'Crea (o promueve a) un usuario administrador real. Usar esto en producción en vez de UserSeeder, que solo crea cuentas de demostración con contraseña conocida.';

    public function handle(): int
    {
        $name = $this->ask('Nombre completo del administrador');
        $email = $this->ask('Email');

        $validator = Validator::make(compact('name', 'email'), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $existing = User::where('email', $email)->first();
        if ($existing) {
            if (!$this->confirm("Ya existe un usuario con ese email ({$existing->name}). ¿Asignarle el rol admin en vez de crear una cuenta nueva?")) {
                return self::FAILURE;
            }
            $existing->roles()->firstOrCreate(['role' => 'admin']);
            $this->info("Listo: {$existing->email} ahora tiene rol admin.");
            return self::SUCCESS;
        }

        $password = $this->secret('Contraseña (mínimo 10 caracteres)');
        $passwordConfirm = $this->secret('Confirmá la contraseña');

        if ($password !== $passwordConfirm) {
            $this->error('Las contraseñas no coinciden.');
            return self::FAILURE;
        }
        if (strlen((string) $password) < 10) {
            $this->error('La contraseña debe tener al menos 10 caracteres.');
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
        UserRole::create(['user_id' => $user->id, 'role' => 'admin']);

        $this->info("Usuario administrador \"{$user->name}\" <{$user->email}> creado correctamente.");
        return self::SUCCESS;
    }
}
