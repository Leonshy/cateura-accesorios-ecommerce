<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneAbandonedGuestCartsTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(): Product
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Producto de prueba',
            'slug' => 'producto-' . uniqid(),
            'price' => 30000,
            'stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_it_deletes_guest_carts_untouched_for_more_than_5_days(): void
    {
        $cart = Cart::create(['session_id' => 'abandonado']);
        $cart->forceFill(['updated_at' => now()->subDays(6)])->save();

        $this->artisan('app:prune-abandoned-guest-carts');

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    public function test_it_keeps_guest_carts_updated_within_the_last_5_days(): void
    {
        $cart = Cart::create(['session_id' => 'reciente']);
        $cart->forceFill(['updated_at' => now()->subDays(2)])->save();

        $this->artisan('app:prune-abandoned-guest-carts');

        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
    }

    public function test_it_never_deletes_carts_belonging_to_a_registered_user(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id, 'session_id' => 'de-usuario']);
        $cart->forceFill(['updated_at' => now()->subDays(30)])->save();

        $this->artisan('app:prune-abandoned-guest-carts');

        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
    }

    public function test_deleting_an_abandoned_cart_also_deletes_its_items(): void
    {
        $product = $this->makeProduct();
        $cart = Cart::create(['session_id' => 'con-items']);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);
        $cart->forceFill(['updated_at' => now()->subDays(10)])->save();

        $this->artisan('app:prune-abandoned-guest-carts');

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
