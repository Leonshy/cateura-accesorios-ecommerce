<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentAdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    public function test_home_content_page_saves_only_home_fields(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.content.home.update'), [
            'historia_eyebrow' => 'Nuestro camino',
            'historia_text' => 'Texto de inicio actualizado',
        ])->assertRedirect();

        $this->assertEquals('Nuestro camino', SiteSetting::group('content_home')['historia_eyebrow']);
        $this->assertArrayNotHasKey('historia_eyebrow', SiteSetting::group('content_artisans'));
        $this->assertArrayNotHasKey('historia_eyebrow', SiteSetting::group('content_about'));
    }

    public function test_artisans_content_page_saves_only_artisans_fields(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.content.artisans.update'), [
            'artisans_title' => 'Nuestras creadoras',
        ])->assertRedirect();

        $this->assertEquals('Nuestras creadoras', SiteSetting::group('content_artisans')['artisans_title']);
        $this->assertArrayNotHasKey('artisans_title', SiteSetting::group('content_home'));
        $this->assertArrayNotHasKey('artisans_title', SiteSetting::group('content_about'));
    }

    public function test_about_content_page_saves_only_about_fields(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.content.about.update'), [
            'about_hero_title' => 'Resiliencia hecha a mano',
        ])->assertRedirect();

        $this->assertEquals('Resiliencia hecha a mano', SiteSetting::group('content_about')['about_hero_title']);
        $this->assertArrayNotHasKey('about_hero_title', SiteSetting::group('content_home'));
        $this->assertArrayNotHasKey('about_hero_title', SiteSetting::group('content_artisans'));
    }

    public function test_saving_home_content_does_not_overwrite_artisans_or_about_content(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.content.artisans.update'), ['artisans_title' => 'Título original artesanas']);
        $this->post(route('admin.content.about.update'), ['about_hero_title' => 'Título original nosotros']);

        $this->post(route('admin.content.home.update'), ['historia_eyebrow' => 'Cambio solo en inicio']);

        $this->assertEquals('Título original artesanas', SiteSetting::group('content_artisans')['artisans_title']);
        $this->assertEquals('Título original nosotros', SiteSetting::group('content_about')['about_hero_title']);
    }

    public function test_each_content_page_is_reachable_and_shows_its_own_form(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.content.home'))->assertOk()->assertSee('name="historia_eyebrow"', false);
        $this->get(route('admin.content.artisans'))->assertOk()->assertSee('name="artisans_title"', false);
        $this->get(route('admin.content.about'))->assertOk()->assertSee('name="about_hero_title"', false);
    }

    public function test_each_content_page_shows_the_three_tabs_with_links_to_each_other(): void
    {
        $this->actingAsAdmin();

        foreach (['admin.content.home', 'admin.content.artisans', 'admin.content.about'] as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertOk();
            $response->assertSee('Inicio');
            $response->assertSee('Artesanas');
            $response->assertSee('Nosotros');
            $response->assertSee(route('admin.content.home'), false);
            $response->assertSee(route('admin.content.artisans'), false);
            $response->assertSee(route('admin.content.about'), false);
        }
    }

    public function test_sidebar_shows_a_single_textos_entry_instead_of_three_separate_links(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(route('admin.content.home'));

        $response->assertOk();
        $response->assertSee('Textos');
        $response->assertDontSee('Textos: Inicio');
        $response->assertDontSee('Textos: Artesanas');
        $response->assertDontSee('Textos: Nosotros');
    }

    public function test_home_page_reflects_saved_content(): void
    {
        $this->actingAsAdmin();
        $this->post(route('admin.content.home.update'), ['historia_eyebrow' => 'Texto visible en portada']);

        $this->get(route('home'))->assertSee('Texto visible en portada');
    }

    public function test_artisans_page_reflects_saved_content(): void
    {
        $this->actingAsAdmin();
        $this->post(route('admin.content.artisans.update'), ['artisans_title' => 'Título visible en listado']);

        $this->get(route('artisans.index'))->assertSee('Título visible en listado');
    }

    public function test_about_page_reflects_saved_content(): void
    {
        $this->actingAsAdmin();
        $this->post(route('admin.content.about.update'), ['about_hero_title' => 'Título visible en nosotros']);

        $this->get(route('about'))->assertSee('Título visible en nosotros');
    }

    public function test_legacy_content_route_no_longer_exists(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('admin.settings.content'));
    }

    public function test_pre_existing_content_rows_are_reclassified_by_the_migration(): void
    {
        // Simula datos ya guardados bajo el grupo único "content" antes del fix
        // (la migración ya corrió sin filas que reclasificar al preparar el test,
        // así que se invoca su up() directamente sobre los datos "legados").
        SiteSetting::set('historia_eyebrow', 'Valor legado inicio', 'content');
        SiteSetting::set('artisans_title', 'Valor legado artesanas', 'content');
        SiteSetting::set('about_hero_title', 'Valor legado nosotros', 'content');

        $migration = require database_path('migrations/2026_07_30_152709_split_site_settings_content_group.php');
        $migration->up();

        $this->assertEquals('Valor legado inicio', SiteSetting::group('content_home')['historia_eyebrow'] ?? null);
        $this->assertEquals('Valor legado artesanas', SiteSetting::group('content_artisans')['artisans_title'] ?? null);
        $this->assertEquals('Valor legado nosotros', SiteSetting::group('content_about')['about_hero_title'] ?? null);
    }
}
