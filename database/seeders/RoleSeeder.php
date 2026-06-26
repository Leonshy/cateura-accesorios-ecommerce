<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['name' => 'admin',    'label' => 'Administrador',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editor',   'label' => 'Editor',          'created_at' => now(), 'updated_at' => now()],
            ['name' => 'vendedor', 'label' => 'Vendedor',        'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cliente',  'label' => 'Cliente',         'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
