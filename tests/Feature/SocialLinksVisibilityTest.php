<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLinksVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    public function test_footer_hides_all_social_icons_when_nothing_is_configured(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('aria-label="Instagram"', false);
        $response->assertDontSee('aria-label="Facebook"', false);
        $response->assertDontSee('aria-label="YouTube"', false);
        $response->assertDontSee('aria-label="TikTok"', false);
        $response->assertDontSee('aria-label="WhatsApp"', false);
    }

    public function test_footer_shows_only_the_socials_with_a_complete_url(): void
    {
        SiteSetting::set('instagram_url', 'https://instagram.com/cateuraaccesorios', 'general');
        SiteSetting::set('youtube_url', '', 'general');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('aria-label="Instagram"', false);
        $response->assertDontSee('aria-label="YouTube"', false);
        $response->assertDontSee('aria-label="Facebook"', false);
        $response->assertDontSee('aria-label="TikTok"', false);
    }

    public function test_footer_shows_youtube_and_tiktok_when_configured(): void
    {
        SiteSetting::set('youtube_url', 'https://youtube.com/@cateuraaccesorios', 'general');
        SiteSetting::set('tiktok_url', 'https://tiktok.com/@cateuraaccesorios', 'general');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('aria-label="YouTube"', false);
        $response->assertSee('aria-label="TikTok"', false);
        $response->assertSee('https://youtube.com/@cateuraaccesorios', false);
        $response->assertSee('https://tiktok.com/@cateuraaccesorios', false);
    }

    public function test_footer_hides_an_incomplete_url_that_is_missing_the_http_scheme(): void
    {
        // Un enlace a medio cargar (sin esquema http/https) se considera incompleto y no debe mostrarse.
        SiteSetting::set('instagram_url', 'instagram.com/cateuraaccesorios', 'general');
        SiteSetting::set('facebook_url', 'www.facebook.com/cateuraaccesorios', 'general');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('aria-label="Instagram"', false);
        $response->assertDontSee('aria-label="Facebook"', false);
    }

    public function test_footer_shows_whatsapp_only_when_the_number_has_enough_digits(): void
    {
        SiteSetting::set('whatsapp_number', '123', 'general');
        $response = $this->get(route('home'));
        $response->assertDontSee('aria-label="WhatsApp"', false);

        SiteSetting::set('whatsapp_number', '595981234567', 'general');
        $response = $this->get(route('home'));
        $response->assertSee('aria-label="WhatsApp"', false);
        $response->assertSee('https://wa.me/595981234567', false);
    }

    public function test_admin_general_settings_page_has_youtube_and_tiktok_fields(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(route('admin.settings.general'));

        $response->assertOk();
        $response->assertSee('name="youtube_url"', false);
        $response->assertSee('name="tiktok_url"', false);
    }

    public function test_saving_youtube_and_tiktok_from_the_admin_makes_them_appear_in_the_footer(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.settings.update'), [
            'youtube_url' => 'https://youtube.com/@cateuraaccesorios',
            'tiktok_url' => 'https://tiktok.com/@cateuraaccesorios',
        ])->assertRedirect();

        $response = $this->get(route('home'));
        $response->assertSee('aria-label="YouTube"', false);
        $response->assertSee('aria-label="TikTok"', false);
    }

    public function test_is_complete_url_helper_validates_scheme(): void
    {
        $this->assertTrue(is_complete_url('https://example.com'));
        $this->assertTrue(is_complete_url('http://example.com'));
        $this->assertFalse(is_complete_url('example.com'));
        $this->assertFalse(is_complete_url(''));
        $this->assertFalse(is_complete_url(null));
        $this->assertFalse(is_complete_url('   '));
    }
}
