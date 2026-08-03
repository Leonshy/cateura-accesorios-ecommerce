<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRole(string $role): User
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    private function actingAsAdmin(): User
    {
        return $this->actingAsRole('admin');
    }

    // ── Editor: acceso a catálogo/contenido, bloqueado en ventas y admin-only ──

    public function test_editor_can_access_catalog_and_content_sections(): void
    {
        $this->actingAsRole('editor');

        foreach ([
            'admin.dashboard',
            'admin.products.index',
            'admin.categories.index',
            'admin.artisans.index',
            'admin.posts.index',
            'admin.banners.index',
            'admin.media.index',
            'admin.manual.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_editor_is_forbidden_from_sales_and_admin_only_sections(): void
    {
        $this->actingAsRole('editor');

        foreach ([
            'admin.orders.index',
            'admin.contacts.index',
            'admin.newsletter.index',
            'admin.users.index',
            'admin.content.home',
            'admin.legal.index',
            'admin.settings.general',
            'admin.settings.integrations',
            'admin.settings.shipping',
        ] as $route) {
            $this->get(route($route))->assertForbidden();
        }
    }

    // ── Vendedor: acceso a ventas, bloqueado en catálogo y admin-only ──

    public function test_vendedor_can_access_sales_sections(): void
    {
        $this->actingAsRole('vendedor');

        foreach ([
            'admin.dashboard',
            'admin.orders.index',
            'admin.contacts.index',
            'admin.newsletter.index',
            'admin.manual.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_vendedor_is_forbidden_from_catalog_and_admin_only_sections(): void
    {
        $this->actingAsRole('vendedor');

        foreach ([
            'admin.products.index',
            'admin.categories.index',
            'admin.artisans.index',
            'admin.posts.index',
            'admin.banners.index',
            'admin.media.index',
            'admin.users.index',
            'admin.content.home',
            'admin.legal.index',
            'admin.settings.general',
            'admin.settings.integrations',
            'admin.settings.shipping',
        ] as $route) {
            $this->get(route($route))->assertForbidden();
        }
    }

    // ── Admin: acceso a todo ──

    public function test_admin_can_access_every_section(): void
    {
        $this->actingAsAdmin();

        foreach ([
            'admin.dashboard',
            'admin.products.index',
            'admin.categories.index',
            'admin.artisans.index',
            'admin.posts.index',
            'admin.banners.index',
            'admin.media.index',
            'admin.orders.index',
            'admin.contacts.index',
            'admin.newsletter.index',
            'admin.users.index',
            'admin.content.home',
            'admin.legal.index',
            'admin.settings.general',
            'admin.settings.integrations',
            'admin.settings.shipping',
            'admin.manual.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    // ── Escalación de privilegios: nadie salvo Admin puede tocar roles de usuario ──

    public function test_editor_cannot_reach_the_user_role_management_screen(): void
    {
        $editor = $this->actingAsRole('editor');
        $other = User::factory()->create();

        $this->get(route('admin.users.edit', $other))->assertForbidden();
        $this->patch(route('admin.users.update', $other), ['roles' => ['admin']])->assertForbidden();

        $this->assertFalse($other->fresh()->isAdmin());
    }

    public function test_vendedor_cannot_self_assign_the_admin_role(): void
    {
        $vendedor = $this->actingAsRole('vendedor');

        $this->patch(route('admin.users.update', $vendedor), ['roles' => ['admin', 'vendedor']])->assertForbidden();

        $this->assertFalse($vendedor->fresh()->isAdmin());
    }

    public function test_admin_can_manage_user_roles(): void
    {
        $this->actingAsAdmin();

        $target = User::factory()->create();
        UserRole::create(['user_id' => $target->id, 'role' => 'editor']);

        $this->patch(route('admin.users.update', $target), ['roles' => ['vendedor']])
            ->assertRedirect();

        $target->refresh();
        $this->assertTrue($target->isVendedor());
        $this->assertFalse($target->hasRole('editor'));
    }

    // ── Dashboard: cada rol ve solo sus propios widgets ──

    public function test_editor_dashboard_does_not_show_sales_widgets(): void
    {
        $this->actingAsRole('editor');

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Productos activos');
        $response->assertSee('Stock bajo');
        $response->assertDontSee('Pedidos pendientes');
        $response->assertDontSee('Ingresos confirmados');
    }

    public function test_vendedor_dashboard_does_not_show_catalog_widgets(): void
    {
        $this->actingAsRole('vendedor');

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Pedidos pendientes');
        $response->assertSee('Ingresos confirmados');
        $response->assertDontSee('Productos activos');
        $response->assertDontSee('Stock bajo');
    }

    public function test_admin_dashboard_shows_every_widget(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Productos activos');
        $response->assertSee('Pedidos pendientes');
        $response->assertSee('Stock bajo');
        $response->assertSee('Ingresos confirmados');
        $response->assertSee('Clientes registrados');
    }
}
