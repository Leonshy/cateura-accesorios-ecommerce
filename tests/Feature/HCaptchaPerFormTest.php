<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Services\HCaptchaVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HCaptchaPerFormTest extends TestCase
{
    use RefreshDatabase;

    private function configureKeys(): void
    {
        SiteSetting::set('hcaptcha_site_key', 'test-site-key', 'general');
        SiteSetting::set('hcaptcha_secret_key', 'test-secret-key', 'general');
    }

    public function test_form_is_disabled_by_default_even_with_keys_configured(): void
    {
        $this->configureKeys();

        $this->assertFalse(HCaptchaVerifier::isEnabledFor('contact'));
        $this->assertFalse(HCaptchaVerifier::isEnabledFor('newsletter'));
        $this->assertFalse(HCaptchaVerifier::isEnabledFor('register'));
    }

    public function test_form_stays_disabled_if_enabled_but_keys_are_missing(): void
    {
        SiteSetting::set('hcaptcha_enabled_contact', '1', 'general');

        $this->assertFalse(HCaptchaVerifier::isEnabledFor('contact'), 'Sin claves configuradas, no puede funcionar aunque esté "activado".');
    }

    public function test_enabling_one_form_does_not_enable_the_others(): void
    {
        $this->configureKeys();
        SiteSetting::set('hcaptcha_enabled_contact', '1', 'general');

        $this->assertTrue(HCaptchaVerifier::isEnabledFor('contact'));
        $this->assertFalse(HCaptchaVerifier::isEnabledFor('newsletter'));
        $this->assertFalse(HCaptchaVerifier::isEnabledFor('register'));
    }

    public function test_admin_can_toggle_hcaptcha_independently_per_form(): void
    {
        $admin = \App\Models\User::factory()->create();
        \App\Models\UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('admin.settings.update'), [
            'hcaptcha_site_key' => 'site-key',
            'hcaptcha_secret_key' => 'secret-key',
            'hcaptcha_enabled_contact' => '1',
            // newsletter y register se omiten a propósito (checkbox destildado)
        ])->assertRedirect();

        $this->assertTrue(HCaptchaVerifier::isEnabledFor('contact'));
        $this->assertFalse(HCaptchaVerifier::isEnabledFor('newsletter'));
        $this->assertFalse(HCaptchaVerifier::isEnabledFor('register'));
    }

    public function test_unchecking_a_previously_enabled_form_actually_disables_it(): void
    {
        $this->configureKeys();
        SiteSetting::set('hcaptcha_enabled_contact', '1', 'general');
        $this->assertTrue(HCaptchaVerifier::isEnabledFor('contact'));

        $admin = \App\Models\User::factory()->create();
        \App\Models\UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);

        // El checkbox de "contact" no viaja en el POST porque se destildó.
        $this->post(route('admin.settings.update'), [
            'hcaptcha_site_key' => 'test-site-key',
            'hcaptcha_secret_key' => 'test-secret-key',
        ])->assertRedirect();

        $this->assertFalse(HCaptchaVerifier::isEnabledFor('contact'));
    }

    public function test_contact_form_shows_the_widget_only_when_enabled_for_that_form(): void
    {
        $this->configureKeys();

        $this->get(route('contact'))->assertDontSee('h-captcha', false);

        SiteSetting::set('hcaptcha_enabled_contact', '1', 'general');
        $this->get(route('contact'))->assertSee('h-captcha', false);
        $this->get(route('contact'))->assertSee('test-site-key');
    }

    public function test_contact_submission_is_rejected_without_a_valid_token_when_enabled(): void
    {
        $this->configureKeys();
        SiteSetting::set('hcaptcha_enabled_contact', '1', 'general');
        Http::fake(['hcaptcha.com/*' => Http::response(['success' => false], 200)]);

        $response = $this->post(route('contact.store'), [
            'name' => 'Cliente Test',
            'email' => 'cliente@test.com',
            'message' => 'Este es un mensaje de prueba con más de diez caracteres.',
            'h-captcha-response' => 'token-invalido',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_submission_succeeds_with_a_valid_token_when_enabled(): void
    {
        $this->configureKeys();
        SiteSetting::set('hcaptcha_enabled_contact', '1', 'general');
        Http::fake(['hcaptcha.com/*' => Http::response(['success' => true], 200)]);

        $response = $this->post(route('contact.store'), [
            'name' => 'Cliente Test',
            'email' => 'cliente@test.com',
            'message' => 'Este es un mensaje de prueba con más de diez caracteres.',
            'h-captcha-response' => 'token-valido',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_contact_submission_is_not_blocked_when_hcaptcha_is_disabled_for_that_form(): void
    {
        $this->configureKeys();
        // No se activa hcaptcha_enabled_contact.

        $response = $this->post(route('contact.store'), [
            'name' => 'Cliente Test',
            'email' => 'cliente@test.com',
            'message' => 'Este es un mensaje de prueba con más de diez caracteres.',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame(1, ContactMessage::count());
    }
}
