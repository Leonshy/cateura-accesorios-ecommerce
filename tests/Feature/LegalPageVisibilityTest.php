<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\LegalPage;
use App\Models\Product;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPageVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function makePage(string $key, string $title, bool $isActive = true): LegalPage
    {
        return LegalPage::create([
            'key' => $key,
            'title' => $title,
            'content' => '<p>Contenido de prueba</p>',
            'is_active' => $isActive,
        ]);
    }

    public function test_admin_can_deactivate_the_purchase_policy_page(): void
    {
        $this->actingAsAdmin();
        $this->makePage('compra', 'Políticas de compra');

        $this->patch(route('admin.legal.update', 'compra'), [
            'title' => 'Políticas de compra',
            'content' => '<p>Contenido</p>',
            // is_active omitido => checkbox destildado
        ])->assertRedirect();

        $this->assertFalse(LegalPage::where('key', 'compra')->value('is_active'));
    }

    public function test_admin_can_reactivate_a_deactivated_page(): void
    {
        $this->actingAsAdmin();
        $this->makePage('envio', 'Políticas de envío', false);

        $this->patch(route('admin.legal.update', 'envio'), [
            'title' => 'Políticas de envío',
            'content' => '<p>Contenido</p>',
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertTrue(LegalPage::where('key', 'envio')->value('is_active'));
    }

    public function test_deactivated_page_returns_404_on_the_public_site(): void
    {
        $this->makePage('devoluciones', 'Cambios y devoluciones', false);

        $this->get(route('legal.devoluciones'))->assertNotFound();
    }

    public function test_active_page_is_reachable_on_the_public_site(): void
    {
        $this->makePage('devoluciones', 'Cambios y devoluciones', true);

        $this->get(route('legal.devoluciones'))->assertOk()->assertSee('Contenido de prueba', false);
    }

    public function test_privacy_and_terms_cannot_be_deactivated_even_if_requested(): void
    {
        $this->actingAsAdmin();
        $this->makePage('privacidad', 'Política de privacidad');
        $this->makePage('terminos', 'Términos y condiciones');

        $this->patch(route('admin.legal.update', 'privacidad'), [
            'title' => 'Política de privacidad',
            'content' => '<p>Contenido</p>',
            'is_active' => 0,
        ]);
        $this->patch(route('admin.legal.update', 'terminos'), [
            'title' => 'Términos y condiciones',
            'content' => '<p>Contenido</p>',
            'is_active' => 0,
        ]);

        $this->assertTrue(LegalPage::where('key', 'privacidad')->value('is_active'));
        $this->assertTrue(LegalPage::where('key', 'terminos')->value('is_active'));
        $this->get(route('legal.privacidad'))->assertOk();
        $this->get(route('legal.terminos'))->assertOk();
    }

    public function test_footer_hides_links_for_deactivated_shipping_and_returns_pages(): void
    {
        $this->makePage('envio', 'Políticas de envío', false);
        $this->makePage('devoluciones', 'Cambios y devoluciones', true);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee(route('legal.envio'), false);
        $response->assertSee(route('legal.devoluciones'), false);
    }

    public function test_checkout_consent_text_omits_link_to_deactivated_purchase_policy(): void
    {
        $this->makePage('compra', 'Políticas de compra', false);

        $user = User::factory()->create();
        $this->actingAs($user);
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-de-prueba',
            'price' => 50000,
            'stock' => 5,
            'is_active' => true,
        ]);
        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1, 'unit_price' => 50000]);

        $response = $this->get(route('checkout.index'));

        $response->assertOk();
        $response->assertDontSee(route('legal.compra'), false);
    }

    public function test_admin_index_shows_visibility_status_per_page(): void
    {
        $this->actingAsAdmin();
        $this->makePage('compra', 'Políticas de compra', false);
        $this->makePage('envio', 'Políticas de envío', true);

        $response = $this->get(route('admin.legal.index'));

        $response->assertOk();
        $response->assertSee('Oculta');
        $response->assertSee('Visible');
        $response->assertSee('Siempre visible');
    }
}
