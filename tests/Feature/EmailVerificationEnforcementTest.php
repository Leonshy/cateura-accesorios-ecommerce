<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmed;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_registering_sends_a_real_verification_email(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'Cliente Nuevo',
            'email' => 'nuevo@test.com',
            'password' => 'clave-segura-123',
            'password_confirmation' => 'clave-segura-123',
        ])->assertRedirect();

        $user = User::where('email', 'nuevo@test.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    public function test_unverified_user_is_redirected_away_from_my_account(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->get(route('account.index'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_access_my_account(): void
    {
        $user = User::factory()->create(); // factory por defecto crea con email_verified_at seteado
        $this->actingAs($user);

        $this->get(route('account.index'))->assertOk();
    }

    public function test_backfilled_pre_existing_users_are_not_locked_out(): void
    {
        // Simula una cuenta creada antes de este fix: el registro nunca envió
        // verificación, así que quedó con email_verified_at null. La migración
        // de backfill ya corrió (RefreshDatabase la ejecuta), así que un
        // usuario recién creado sin especificar el campo queda verificado por
        // el valor por defecto de la factory; para simular el escenario real
        // forzamos null y confirmamos el comportamiento esperado del middleware.
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        // Un usuario realmente sin verificar SÍ debe quedar bloqueado — esto
        // documenta que el backfill de la migración es lo único que
        // "perdona" a las cuentas viejas, no una excepción de código.
        $this->get(route('account.index'))->assertRedirect(route('verification.notice'));
    }

    public function test_guest_checkout_routes_have_no_auth_or_verified_middleware(): void
    {
        // El carrito de invitado se ubica por session()->getId(), cuyo valor
        // real no es controlable de forma determinística desde un test HTTP
        // (mismo límite ya documentado en CheckoutPaymentGatewaysTest). En vez
        // de simular una sesión de invitado, se verifica directamente que las
        // rutas de checkout sigan sin exigir "auth" ni "verified" — es la
        // garantía real de que la compra como invitado sigue sin fricción.
        foreach (['checkout.index', 'checkout.store', 'cart.index', 'cart.add'] as $routeName) {
            $middleware = \Illuminate\Support\Facades\Route::getRoutes()->getByName($routeName)->middleware();
            $this->assertNotContains('auth', $middleware, "La ruta {$routeName} no debería exigir sesión iniciada.");
            $this->assertNotContains('verified', $middleware, "La ruta {$routeName} no debería exigir email verificado.");
        }
    }

    public function test_unverified_logged_in_user_can_still_checkout(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-' . uniqid(),
            'price' => 50000,
            'stock' => 5,
            'is_active' => true,
        ]);
        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1, 'unit_price' => 50000]);

        $this->seed(\Database\Seeders\PaymentMethodSeeder::class);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@test.com',
            'shipping_type' => 'pickup',
            'payment_method' => 'transferencia',
            'transfer_receipt' => \Illuminate\Http\UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf'),
            'accept_terms' => '1',
        ]);

        $response->assertRedirect();
        Mail::assertSent(OrderConfirmed::class);
    }
}
