<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactInfoConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function setContactInfo(): void
    {
        SiteSetting::set('contact_address', "Calle Falsa 123\nBarrio Centro, Ciudad del Este", 'general');
        SiteSetting::set('contact_phone', '0981 111 222', 'general');
        SiteSetting::set('contact_email', 'contacto@ejemplo.com', 'general');
        SiteSetting::set('footer_description', 'Descripción de prueba del pie de página.', 'general');
    }

    public function test_footer_and_contact_page_show_the_same_contact_info(): void
    {
        $this->setContactInfo();

        $footer = $this->get(route('home'));
        $contact = $this->get(route('contact'));

        $footer->assertOk();
        $contact->assertOk();

        foreach (['Calle Falsa 123', 'Ciudad del Este', '0981 111 222', 'contacto@ejemplo.com'] as $piece) {
            $footer->assertSee($piece);
            $contact->assertSee($piece);
        }
    }

    public function test_footer_description_comes_from_site_settings(): void
    {
        $this->setContactInfo();

        $this->get(route('home'))->assertSee('Descripción de prueba del pie de página.');
    }

    public function test_footer_description_falls_back_to_default_when_not_configured(): void
    {
        $this->get(route('home'))->assertSee('Accesorios, piezas decorativas y prendas creadas por artesanas del Bañado Sur a partir de materiales reciclados.');
    }

    public function test_admin_can_edit_footer_description(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(route('admin.settings.general'));
        $response->assertOk();
        $response->assertSee('name="footer_description"', false);

        $this->post(route('admin.settings.update'), [
            'footer_description' => 'Nueva descripción editada desde el panel.',
        ])->assertRedirect();

        $this->get(route('home'))->assertSee('Nueva descripción editada desde el panel.');
    }

    public function test_header_top_bar_uses_the_same_configured_phone_and_instagram(): void
    {
        $this->setContactInfo();
        SiteSetting::set('instagram_url', 'https://www.instagram.com/miempresa', 'general');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('0981 111 222');
        $response->assertSee('https://www.instagram.com/miempresa', false);
    }

    public function test_paraguay_tel_href_adds_country_code_to_local_format(): void
    {
        $this->assertEquals('tel:+595981877315', paraguay_tel_href('0981 877 315'));
        $this->assertEquals('tel:+595981877315', paraguay_tel_href('0981877315'));
    }

    public function test_paraguay_tel_href_keeps_already_international_numbers(): void
    {
        $this->assertEquals('tel:+595981877315', paraguay_tel_href('+595 981 877 315'));
    }

    public function test_floating_whatsapp_button_uses_the_configured_whatsapp_number(): void
    {
        SiteSetting::set('whatsapp_number', '595987654321', 'general');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('https://wa.me/595987654321', false);
    }

    public function test_contact_page_address_and_phone_blocks_hide_when_not_configured(): void
    {
        SiteSetting::set('contact_address', '', 'general');
        SiteSetting::set('contact_phone', '', 'general');
        SiteSetting::set('contact_email', '', 'general');

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertDontSee('Dirección</p>', false);
        $response->assertDontSee('Teléfono / WhatsApp', false);
    }
}
