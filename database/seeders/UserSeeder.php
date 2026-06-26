<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrador',   'email' => 'admin@cateura.test',    'role' => 'admin'],
            ['name' => 'Editor',          'email' => 'editor@cateura.test',   'role' => 'editor'],
            ['name' => 'Vendedor',        'email' => 'vendedor@cateura.test', 'role' => 'vendedor'],
            ['name' => 'Cliente Demo',    'email' => 'cliente@cateura.test',  'role' => null],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password'), 'email_verified_at' => now()]
            );
            if ($u['role']) {
                UserRole::firstOrCreate(['user_id' => $user->id, 'role' => $u['role']]);
            }
        }
    }
}
