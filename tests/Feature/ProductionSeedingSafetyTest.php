<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeedingSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeders_do_not_run_when_the_environment_is_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--force' => true]);

        $this->assertDatabaseMissing('users', ['email' => 'admin@cateura.test']);
        $this->assertSame(0, Category::count());
    }

    public function test_base_seeders_still_run_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--force' => true]);

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('payment_methods', ['key' => 'transferencia']);
        $this->assertDatabaseHas('legal_pages', ['key' => 'privacidad']);
    }

    public function test_demo_seeders_do_run_in_local_and_testing_environments(): void
    {
        $this->seed(); // el entorno de test ya es "testing"

        $this->assertDatabaseHas('users', ['email' => 'admin@cateura.test']);
        $this->assertTrue(Category::count() > 0);
    }

    public function test_crear_admin_command_creates_a_real_admin_user(): void
    {
        $this->artisan('app:crear-admin')
            ->expectsQuestion('Nombre completo del administrador', 'Ana Benítez')
            ->expectsQuestion('Email', 'ana@asociacion.test')
            ->expectsQuestion('Contraseña (mínimo 10 caracteres)', 'clave-super-segura')
            ->expectsQuestion('Confirmá la contraseña', 'clave-super-segura')
            ->assertExitCode(0);

        $user = User::where('email', 'ana@asociacion.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('clave-super-segura', $user->password));
    }

    public function test_crear_admin_command_rejects_mismatched_passwords(): void
    {
        $this->artisan('app:crear-admin')
            ->expectsQuestion('Nombre completo del administrador', 'Ana Benítez')
            ->expectsQuestion('Email', 'ana2@asociacion.test')
            ->expectsQuestion('Contraseña (mínimo 10 caracteres)', 'clave-super-segura')
            ->expectsQuestion('Confirmá la contraseña', 'otra-clave-distinta')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'ana2@asociacion.test']);
    }

    public function test_crear_admin_command_rejects_short_passwords(): void
    {
        $this->artisan('app:crear-admin')
            ->expectsQuestion('Nombre completo del administrador', 'Ana Benítez')
            ->expectsQuestion('Email', 'ana3@asociacion.test')
            ->expectsQuestion('Contraseña (mínimo 10 caracteres)', 'corta')
            ->expectsQuestion('Confirmá la contraseña', 'corta')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'ana3@asociacion.test']);
    }

    public function test_crear_admin_command_can_promote_an_existing_user_instead_of_duplicating(): void
    {
        $existing = User::factory()->create(['email' => 'yaexiste@asociacion.test']);
        UserRole::create(['user_id' => $existing->id, 'role' => 'vendedor']);

        $this->artisan('app:crear-admin')
            ->expectsQuestion('Nombre completo del administrador', 'Nombre ignorado')
            ->expectsQuestion('Email', 'yaexiste@asociacion.test')
            ->expectsConfirmation('Ya existe un usuario con ese email (' . $existing->name . '). ¿Asignarle el rol admin en vez de crear una cuenta nueva?', 'yes')
            ->assertExitCode(0);

        $this->assertSame(1, User::where('email', 'yaexiste@asociacion.test')->count());
        $this->assertTrue($existing->fresh()->isAdmin());
    }
}
