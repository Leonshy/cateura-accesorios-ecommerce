<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankTransferDetailsTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function makeTransferMethod(array $credentials = []): PaymentMethod
    {
        return PaymentMethod::create([
            'key' => 'transferencia',
            'name' => 'Transferencia bancaria',
            'credentials' => $credentials,
            'is_active' => true,
            'sandbox' => false,
            'order' => 1,
        ]);
    }

    private function makeCartForCheckout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-de-prueba',
            'price' => 100000,
            'stock' => 10,
            'is_active' => true,
        ]);
        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100000]);
    }

    public function test_admin_can_edit_bank_transfer_details_from_integrations(): void
    {
        $this->actingAsAdmin();
        $method = $this->makeTransferMethod();

        $response = $this->get(route('admin.settings.integrations'));
        $response->assertOk();
        $response->assertSee('name="credentials[bank_name]"', false);
        $response->assertSee('name="credentials[bank_account]"', false);
        $response->assertSee('name="credentials[bank_titular]"', false);
        $response->assertSee('name="credentials[bank_ci]"', false);

        $this->patch(route('admin.settings.payment', $method), [
            'is_active' => 1,
            'credentials' => [
                'bank_name' => 'Banco Test',
                'bank_account' => '123-456',
                'bank_titular' => 'Asociación de Prueba',
                'bank_ci' => '9.999.999',
            ],
        ])->assertRedirect();

        $method->refresh();
        $this->assertEquals('Banco Test', $method->credentials['bank_name']);
        $this->assertEquals('123-456', $method->credentials['bank_account']);
        $this->assertEquals('Asociación de Prueba', $method->credentials['bank_titular']);
        $this->assertEquals('9.999.999', $method->credentials['bank_ci']);
    }

    public function test_checkout_shows_bank_details_as_soon_as_transfer_method_is_present(): void
    {
        $this->makeTransferMethod([
            'bank_name' => 'Banco Test',
            'bank_account' => '123-456',
            'bank_titular' => 'Asociación de Prueba',
            'bank_ci' => '9.999.999',
        ]);
        $this->makeCartForCheckout();

        $response = $this->get(route('checkout.index'));

        $response->assertOk();
        // Los datos bancarios deben estar en el HTML del checkout (visibles al elegir "Transferencia"),
        // no solo mencionados como algo que se mostrará después de confirmar el pedido.
        $response->assertSee('Banco Test');
        $response->assertSee('123-456');
        $response->assertSee('Asociación de Prueba');
        $response->assertSee('9.999.999');
        $response->assertDontSee('Recibirás los datos para transferir al confirmar el pedido');
    }

    public function test_checkout_does_not_promise_bank_data_will_arrive_later(): void
    {
        $this->makeTransferMethod([
            'bank_name' => 'Banco Test',
            'bank_account' => '123-456',
            'bank_titular' => 'Asociación de Prueba',
            'bank_ci' => '9.999.999',
        ]);
        $this->makeCartForCheckout();

        $response = $this->get(route('checkout.index'));

        $response->assertOk();
        $response->assertSee('Vas a ver los datos de la cuenta para transferir');
    }

    public function test_confirmation_page_reads_the_same_bank_details_configured_in_admin(): void
    {
        $this->makeTransferMethod([
            'bank_name' => 'Banco Confirmación',
            'bank_account' => '999-000',
            'bank_titular' => 'Titular Confirmación',
            'bank_ci' => '1.111.111',
        ]);
        $this->makeCartForCheckout();

        $store = $this->post(route('checkout.store'), [
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@test.com',
            'shipping_type' => 'pickup',
            'payment_method' => 'transferencia',
            'transfer_receipt' => \Illuminate\Http\UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf'),
            'accept_terms' => '1',
        ]);

        $order = \App\Models\Order::first();
        $this->assertNotNull($order);

        $confirmation = $this->get(route('checkout.confirmation', $order->order_number));
        $confirmation->assertOk();
        $confirmation->assertSee('Banco Confirmación');
        $confirmation->assertSee('999-000');
        $confirmation->assertSee('Titular Confirmación');
        $confirmation->assertSee('1.111.111');
    }
}
