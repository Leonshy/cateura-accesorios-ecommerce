<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Estos seeders son datos estructurales/base: seguros de correr siempre,
        // incluso en producción (roles, configuración del sitio, métodos de
        // pago habilitados, páginas legales).
        $this->call([
            RoleSeeder::class,
            SiteSettingSeeder::class,
            PaymentMethodSeeder::class,
            LegalPageSeeder::class,
        ]);

        // Estos son datos de DEMOSTRACIÓN (usuarios de prueba con contraseña
        // "password", catálogo y contenido ficticio) para desarrollo local.
        // Nunca deben correr en producción: en vez de un usuario admin con
        // contraseña conocida, usar `php artisan app:crear-admin`.
        if (app()->environment(['local', 'testing'])) {
            $this->call([
                UserSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
                ArtisanSeeder::class,
                PostSeeder::class,
                BannerSeeder::class,
            ]);
        }
    }
}
