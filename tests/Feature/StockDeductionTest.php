<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockDeductionTest extends TestCase
{
    use RefreshDatabase;

    private function seedBase(): void
    {
        $this->seed(\Database\Seeders\PaymentMethodSeeder::class);
    }

    private function makeProduct(int $stock, int $price = 100000): Product
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);
        return Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera única',
            'slug' => 'pulsera-unica-' . uniqid(),
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    private function makeCartFor(Product $product, int $quantity = 1): Cart
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);

        return $cart;
    }

    private function checkoutPayload(string $paymentMethod = 'transferencia'): array
    {
        return [
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@test.com',
            'shipping_type' => 'pickup',
            'payment_method' => $paymentMethod,
            'accept_terms' => '1',
        ];
    }

    public function test_stock_is_decremented_when_an_order_is_created(): void
    {
        Storage::fake('public');
        $this->seedBase();
        $product = $this->makeProduct(stock: 10);
        $this->makeCartFor($product, 3);

        $payload = $this->checkoutPayload();
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');

        $this->post(route('checkout.store'), $payload)->assertRedirect();

        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_second_purchase_of_the_last_unit_is_rejected_without_overselling(): void
    {
        // Simula la condición de carrera: dos intentos de compra de la última
        // unidad disponible. El primero debe pasar y descontar el stock; el
        // segundo, al bloquear la fila y revalidar, debe encontrar 0 y fallar
        // en vez de vender de más.
        Storage::fake('public');
        $this->seedBase();
        $product = $this->makeProduct(stock: 1);

        $this->makeCartFor($product, 1);
        $firstPayload = $this->checkoutPayload();
        $firstPayload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');
        $this->post(route('checkout.store'), $firstPayload)->assertRedirect(route('checkout.confirmation', Order::first()->order_number));

        $this->makeCartFor($product, 1);
        $secondPayload = $this->checkoutPayload();
        $secondPayload['transfer_receipt'] = UploadedFile::fake()->create('comprobante2.pdf', 100, 'application/pdf');
        $second = $this->post(route('checkout.store'), $secondPayload);

        $second->assertRedirect(route('cart.index'));
        $second->assertSessionHas('error');

        $this->assertSame(0, $product->fresh()->stock);
        $this->assertSame(1, Order::count(), 'No debería haberse creado un segundo pedido.');
    }

    public function test_stock_never_goes_negative_across_a_burst_of_attempts(): void
    {
        Storage::fake('public');
        $this->seedBase();
        $product = $this->makeProduct(stock: 2);

        $succeeded = 0;
        for ($i = 0; $i < 5; $i++) {
            $this->makeCartFor($product, 1);
            $payload = $this->checkoutPayload();
            $payload['transfer_receipt'] = UploadedFile::fake()->create("comprobante{$i}.pdf", 100, 'application/pdf');
            $response = $this->post(route('checkout.store'), $payload);
            if (!$response->isRedirect(route('cart.index'))) {
                $succeeded++;
            }
        }

        $this->assertSame(2, $succeeded, 'Solo deberían pasar tantas compras como stock había.');
        $this->assertSame(0, $product->fresh()->stock);
        $this->assertGreaterThanOrEqual(0, $product->fresh()->stock);
    }

    public function test_stock_is_restored_when_bancard_webhook_rejects_the_payment(): void
    {
        $this->seedBase();
        $product = $this->makeProduct(stock: 5);
        $this->makeCartFor($product, 2);

        PaymentMethod::where('key', 'bancard')->update([
            'is_active' => true,
            'sandbox' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);

        Http::fake([
            '*/single_buy/confirmations' => Http::response(['confirmation' => ['response_code' => '05']], 200),
            'vpos.infonet.com.py*' => Http::response(['process_id' => 'proc_1'], 200),
        ]);

        $this->post(route('checkout.store'), $this->checkoutPayload('bancard'));
        $order = Order::first();
        $this->assertSame(3, $product->fresh()->stock, 'El stock debe descontarse ni bien se crea el pedido.');

        $this->post(route('checkout.webhooks.bancard'), [
            'operation' => ['shop_process_id' => $order->bancard_process_id, 'response' => 'S', 'response_code' => '00'],
        ])->assertOk();

        $this->assertSame(5, $product->fresh()->stock, 'Al rechazarse el pago, el stock debe volver.');
    }

    public function test_stock_is_not_restored_twice_if_the_webhook_fires_more_than_once(): void
    {
        $this->seedBase();
        $product = $this->makeProduct(stock: 5);
        $this->makeCartFor($product, 2);

        PaymentMethod::where('key', 'bancard')->update([
            'is_active' => true,
            'sandbox' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);

        Http::fake([
            '*/single_buy/confirmations' => Http::response(['confirmation' => ['response_code' => '05']], 200),
            'vpos.infonet.com.py*' => Http::response(['process_id' => 'proc_1'], 200),
        ]);

        $this->post(route('checkout.store'), $this->checkoutPayload('bancard'));
        $order = Order::first();

        $webhookPayload = [
            'operation' => ['shop_process_id' => $order->bancard_process_id, 'response' => 'S', 'response_code' => '00'],
        ];
        $this->post(route('checkout.webhooks.bancard'), $webhookPayload)->assertOk();
        $this->post(route('checkout.webhooks.bancard'), $webhookPayload)->assertOk();

        $this->assertSame(5, $product->fresh()->stock, 'Reintentar el webhook no debe sumar stock de más.');
    }

    public function test_stock_stays_deducted_when_bancard_payment_is_approved(): void
    {
        $this->seedBase();
        $product = $this->makeProduct(stock: 5);
        $this->makeCartFor($product, 2);

        PaymentMethod::where('key', 'bancard')->update([
            'is_active' => true,
            'sandbox' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);

        Http::fake([
            '*/single_buy/confirmations' => Http::response(['confirmation' => ['response_code' => '00']], 200),
            'vpos.infonet.com.py*' => Http::response(['process_id' => 'proc_1'], 200),
        ]);

        $this->post(route('checkout.store'), $this->checkoutPayload('bancard'));
        $order = Order::first();

        $this->post(route('checkout.webhooks.bancard'), [
            'operation' => ['shop_process_id' => $order->bancard_process_id, 'response' => 'S', 'response_code' => '00'],
        ])->assertOk();

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_admin_cancelling_an_order_restores_its_stock(): void
    {
        Storage::fake('public');
        $this->seedBase();
        $product = $this->makeProduct(stock: 5);
        $this->makeCartFor($product, 2);

        $payload = $this->checkoutPayload();
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');
        $this->post(route('checkout.store'), $payload);
        $order = Order::first();
        $this->assertSame(3, $product->fresh()->stock);

        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);

        $this->patch(route('admin.orders.status', $order), [
            'status' => 'cancelado',
            'payment_status' => 'rechazado',
        ])->assertRedirect();

        $this->assertSame(5, $product->fresh()->stock);
    }
}
