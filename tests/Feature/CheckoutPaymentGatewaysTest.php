<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutPaymentGatewaysTest extends TestCase
{
    use RefreshDatabase;

    private function seedBase(): void
    {
        $this->seed(\Database\Seeders\PaymentMethodSeeder::class);
    }

    /**
     * Usamos un usuario autenticado (en vez de carrito de invitado) porque el carrito
     * de invitado se ubica por session()->getId(), cuyo valor real no es controlable
     * de forma determinística desde un test HTTP.
     */
    private function makeCartWithProduct(int $price = 100000): Cart
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-de-prueba',
            'price' => $price,
            'stock' => 10,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $price,
        ]);

        return $cart;
    }

    private function checkoutPayload(string $paymentMethod, string $shippingType = 'pickup'): array
    {
        return [
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@test.com',
            'customer_phone' => '0981234567',
            'shipping_type' => $shippingType,
            'address_line1' => 'Calle Falsa 123',
            'address_city' => 'Asunción',
            'address_department' => 'Central',
            'payment_method' => $paymentMethod,
            'accept_terms' => '1',
        ];
    }

    public function test_transferencia_bancaria_crea_orden_y_vacia_carrito(): void
    {
        Storage::fake('public');
        $this->seedBase();
        $this->makeCartWithProduct();

        $payload = $this->checkoutPayload('transferencia');
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');

        $response = $this->post(route('checkout.store'), $payload);

        $order = Order::first();
        $this->assertNotNull($order, 'La orden debería haberse creado.');
        $this->assertSame('pendiente_confirmacion', $order->payment_status);
        $this->assertSame('pendiente', $order->status);
        $this->assertSame(100000, $order->subtotal);
        $this->assertSame(0, $order->shipping_cost);
        $this->assertSame(100000, $order->total);
        $this->assertSame(1, $order->items()->count());
        $this->assertNotNull($order->transfer_receipt, 'El comprobante debería haberse guardado.');
        Storage::disk('public')->assertExists($order->transfer_receipt);
        $response->assertRedirect(route('checkout.confirmation', $order->order_number));

        $this->assertSame(0, Cart::count(), 'El carrito debería eliminarse tras confirmar la orden.');

        $confirmation = $this->get(route('checkout.confirmation', $order->order_number));
        $confirmation->assertOk();
        $confirmation->assertSee($order->order_number);
        $confirmation->assertSee('Datos para transferencia bancaria');
    }

    public function test_bancard_crea_pago_y_redirige_a_la_pasarela(): void
    {
        $this->seedBase();
        $this->makeCartWithProduct();

        PaymentMethod::where('key', 'bancard')->update([
            'is_active' => true,
            'sandbox' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);

        Http::fake([
            'vpos.infonet.com.py*' => Http::response(['process_id' => 'proc_123456'], 200),
        ]);

        $response = $this->post(route('checkout.store'), $this->checkoutPayload('bancard'));

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertNotNull($order->bancard_process_id);
        $response->assertRedirect();
        $this->assertStringContainsString('payment/card?process_id=proc_123456', $response->headers->get('Location'));

        Http::assertSent(function ($request) {
            $body = $request->data();
            return str_contains($request->url(), '/single_buy')
                && $body['operation']['amount'] === '100000.00'
                && $body['operation']['currency'] === 'PYG'
                && $body['operation']['token'] === md5('test_private' . $body['operation']['shop_process_id'] . '100000.00' . 'PYG');
        });

        // Simular webhook de aprobación de Bancard
        $webhook = $this->post(route('checkout.webhooks.bancard'), [
            'operation' => [
                'shop_process_id' => $order->bancard_process_id,
                'response' => 'S',
                'response_code' => '00',
            ],
        ]);
        $webhook->assertOk();
        $order->refresh();
        $this->assertSame('pagado', $order->payment_status);
        $this->assertSame('confirmado', $order->status);
    }

    public function test_pagopar_crea_transaccion_y_redirige_a_pagopar(): void
    {
        $this->seedBase();
        $this->makeCartWithProduct();

        PaymentMethod::where('key', 'pagopar')->update([
            'is_active' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);

        Http::fake([
            'api.pagopar.com/api/comercios/2.0/iniciar-transaccion' => Http::response([
                'respuesta' => true,
                'resultado' => [['data' => 'hash_abc123', 'pedido' => 'pp_98765']],
            ], 200),
        ]);

        $response = $this->post(route('checkout.store'), $this->checkoutPayload('pagopar'));

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame('hash_abc123', $order->pagopar_hash);
        $response->assertRedirect('https://www.pagopar.com/pagos/hash_abc123');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'iniciar-transaccion')
                && $request->data()['id_pedido_comercio'] !== null;
        });

        // Simular webhook de Pagopar con token válido (sha1(private_key . hash_pedido))
        Http::fake([
            'api.pagopar.com/api/pedidos/1.1/traer' => Http::response([
                'resultado' => [['pagado' => true, 'cancelado' => false]],
            ], 200),
        ]);

        $token = sha1('test_private' . 'hash_abc123');
        $webhook = $this->postJson(route('checkout.webhooks.pagopar'), [
            'resultado' => [['hash_pedido' => 'hash_abc123', 'token' => $token]],
        ]);
        $webhook->assertOk();
        $order->refresh();
        $this->assertSame('pagado', $order->payment_status);
        $this->assertSame('confirmado', $order->status);
    }

    public function test_pagopar_webhook_rechaza_token_invalido(): void
    {
        $this->seedBase();
        PaymentMethod::where('key', 'pagopar')->update([
            'is_active' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);

        $webhook = $this->postJson(route('checkout.webhooks.pagopar'), [
            'resultado' => [['hash_pedido' => 'hash_abc123', 'token' => 'token-falso']],
        ]);

        $webhook->assertStatus(403);
    }

    public function test_metodo_de_pago_inactivo_es_rechazado(): void
    {
        $this->seedBase();
        $this->makeCartWithProduct();

        // pagopar está inactivo por defecto en el seeder
        $response = $this->post(route('checkout.store'), $this->checkoutPayload('pagopar'));

        $response->assertRedirect();
        $this->assertSame(0, Order::count(), 'No debería crearse una orden con un método de pago inactivo.');
    }
}
