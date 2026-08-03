<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    public function test_admin_can_send_a_password_reset_link_to_any_user(): void
    {
        Notification::fake();
        $this->actingAsAdmin();
        $target = User::factory()->create(['email' => 'cliente@test.com']);

        $response = $this->post(route('admin.users.send-password-reset', $target));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Notification::assertSentTo($target, ResetPassword::class);
    }

    public function test_the_reset_link_actually_works_to_set_a_new_password(): void
    {
        $this->actingAsAdmin();
        $target = User::factory()->create(['email' => 'cliente@test.com']);

        $this->post(route('admin.users.send-password-reset', $target))->assertRedirect();

        $token = \Illuminate\Support\Facades\Password::createToken($target);

        // El formulario de restablecimiento requiere invitado: la persona que
        // resetea su contraseña no está logueada como el admin que envió el link.
        $this->post(route('logout'));

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => $target->email,
            'password' => 'nueva-clave-segura',
            'password_confirmation' => 'nueva-clave-segura',
        ]);

        $response->assertRedirect();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('nueva-clave-segura', $target->fresh()->password));
    }

    public function test_editor_cannot_send_a_password_reset_link(): void
    {
        Notification::fake();
        $editor = User::factory()->create();
        UserRole::create(['user_id' => $editor->id, 'role' => 'editor']);
        $this->actingAs($editor);

        $target = User::factory()->create();

        $this->post(route('admin.users.send-password-reset', $target))->assertForbidden();
        Notification::assertNothingSent();
    }

    public function test_vendedor_cannot_send_a_password_reset_link(): void
    {
        Notification::fake();
        $vendedor = User::factory()->create();
        UserRole::create(['user_id' => $vendedor->id, 'role' => 'vendedor']);
        $this->actingAs($vendedor);

        $target = User::factory()->create();

        $this->post(route('admin.users.send-password-reset', $target))->assertForbidden();
        Notification::assertNothingSent();
    }

    public function test_guests_cannot_trigger_a_reset_link_from_the_admin_route(): void
    {
        $target = User::factory()->create();

        $response = $this->post(route('admin.users.send-password-reset', $target));

        $response->assertRedirect(route('login'));
    }
}
