<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmed;
use App\Mail\OrderStatusChanged;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderEmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function seedBase(): void
    {
        $this->seed(\Database\Seeders\PaymentMethodSeeder::class);
    }

    private function makeCartWithProduct(int $price = 100000): Cart
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-de-prueba-' . uniqid(),
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
            'shipping_type' => $shippingType,
            'payment_method' => $paymentMethod,
            'accept_terms' => '1',
        ];
    }

    public function test_order_confirmation_email_is_sent_after_a_transferencia_checkout(): void
    {
        Mail::fake();
        Storage::fake('public');
        $this->seedBase();
        $this->makeCartWithProduct();

        $payload = $this->checkoutPayload('transferencia');
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');

        $this->post(route('checkout.store'), $payload)->assertRedirect();

        $order = Order::first();
        Mail::assertSent(OrderConfirmed::class, function ($mail) use ($order) {
            return $mail->order->is($order) && $mail->hasTo('cliente@test.com');
        });
    }

    public function test_order_confirmation_email_contains_order_number_and_total(): void
    {
        Mail::fake();
        Storage::fake('public');
        $this->seedBase();
        $this->makeCartWithProduct(100000);

        $payload = $this->checkoutPayload('transferencia');
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');
        $this->post(route('checkout.store'), $payload);

        $order = Order::first();

        Mail::assertSent(OrderConfirmed::class, function ($mail) use ($order) {
            $rendered = $mail->render();
            return str_contains($rendered, $order->order_number)
                && str_contains($rendered, 'Pulsera de prueba');
        });
    }

    public function test_order_confirmation_email_includes_bank_details_for_transferencia(): void
    {
        Mail::fake();
        Storage::fake('public');
        $this->seedBase();
        $this->makeCartWithProduct();
        PaymentMethod::where('key', 'transferencia')->update([
            'credentials' => [
                'bank_name' => 'Banco Test',
                'bank_account' => '123-456',
                'bank_titular' => 'Asociación Test',
                'bank_ci' => '9.999.999',
            ],
        ]);

        $payload = $this->checkoutPayload('transferencia');
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');
        $this->post(route('checkout.store'), $payload);

        Mail::assertSent(OrderConfirmed::class, function ($mail) {
            $rendered = $mail->render();
            return str_contains($rendered, 'Banco Test') && str_contains($rendered, '123-456');
        });
    }

    public function test_order_confirmation_email_is_sent_after_bancard_checkout(): void
    {
        Mail::fake();
        $this->seedBase();
        $this->makeCartWithProduct();

        PaymentMethod::where('key', 'bancard')->update([
            'is_active' => true,
            'sandbox' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);
        Http::fake(['vpos.infonet.com.py*' => Http::response(['process_id' => 'proc_1'], 200)]);

        $this->post(route('checkout.store'), $this->checkoutPayload('bancard'));

        Mail::assertSent(OrderConfirmed::class);
    }

    public function test_order_confirmation_email_is_not_sent_if_bancard_fails_to_start(): void
    {
        Mail::fake();
        $this->seedBase();
        $this->makeCartWithProduct();

        PaymentMethod::where('key', 'bancard')->update([
            'is_active' => true,
            'sandbox' => true,
            'credentials' => ['public_key' => 'test_public', 'private_key' => 'test_private'],
        ]);
        Http::fake(['vpos.infonet.com.py*' => Http::response([], 500)]);

        $this->post(route('checkout.store'), $this->checkoutPayload('bancard'));

        Mail::assertNotSent(OrderConfirmed::class);
    }

    public function test_a_failed_mail_send_does_not_break_the_checkout(): void
    {
        Storage::fake('public');
        $this->seedBase();
        $this->makeCartWithProduct();

        Mail::shouldReceive('to->send')->andThrow(new \RuntimeException('SMTP no configurado'));
        Log::shouldReceive('error')->once();

        $payload = $this->checkoutPayload('transferencia');
        $payload['transfer_receipt'] = UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf');

        $response = $this->post(route('checkout.store'), $payload);

        $order = Order::first();
        $this->assertNotNull($order, 'El pedido debe crearse aunque el correo falle.');
        $response->assertRedirect(route('checkout.confirmation', $order->order_number));
    }

    public function test_status_changed_email_is_sent_when_admin_updates_the_order(): void
    {
        Mail::fake();
        $order = Order::create([
            'order_number' => Order::generateNumber(),
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@test.com',
            'payment_method' => 'transferencia',
            'payment_status' => 'pendiente_confirmacion',
            'shipping_method' => 'retiro_tienda',
            'shipping_cost' => 0,
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'pendiente',
        ]);

        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);

        $this->patch(route('admin.orders.status', $order), [
            'status' => 'confirmado',
            'payment_status' => 'pagado',
        ])->assertRedirect();

        Mail::assertSent(OrderStatusChanged::class, function ($mail) use ($order) {
            return $mail->order->is($order) && $mail->hasTo('cliente@test.com');
        });
    }

    public function test_status_changed_email_is_not_sent_if_nothing_actually_changed(): void
    {
        Mail::fake();
        $order = Order::create([
            'order_number' => Order::generateNumber(),
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@test.com',
            'payment_method' => 'transferencia',
            'payment_status' => 'pendiente_confirmacion',
            'shipping_method' => 'retiro_tienda',
            'shipping_cost' => 0,
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'pendiente',
        ]);

        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);

        $this->patch(route('admin.orders.status', $order), [
            'status' => 'pendiente',
            'payment_status' => 'pendiente_confirmacion',
            'internal_notes' => 'Solo una nota interna, sin cambio de estado.',
        ])->assertRedirect();

        Mail::assertNotSent(OrderStatusChanged::class);
    }
}
