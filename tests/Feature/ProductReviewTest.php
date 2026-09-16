<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);

        return Product::create([
            'category_id' => $category->id,
            'name'        => 'Collar de aluminio reciclado',
            'slug'        => 'collar-' . uniqid(),
            'price'       => 80000,
            'stock'       => 5,
            'is_active'   => true,
        ]);
    }

    public function test_guest_can_submit_a_review_that_stays_unapproved(): void
    {
        $product = $this->product();

        $response = $this->post(route('shop.product.reviews.store', $product->slug), [
            'rating'        => 5,
            'reviewer_name' => 'Ana Invitada',
            'comment'       => 'Hermoso producto, muy bien terminado.',
        ]);

        $response->assertRedirect(route('shop.product', ['slug' => $product->slug, 'tab' => 'reviews']));

        $this->assertDatabaseHas('product_reviews', [
            'product_id'    => $product->id,
            'reviewer_name' => 'Ana Invitada',
            'rating'        => 5,
            'is_approved'   => false,
        ]);
    }

    public function test_guest_review_requires_a_name(): void
    {
        $product = $this->product();

        $this->post(route('shop.product.reviews.store', $product->slug), [
            'rating' => 4,
        ])->assertSessionHasErrors('reviewer_name');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $product = $this->product();

        $this->post(route('shop.product.reviews.store', $product->slug), [
            'rating'        => 6,
            'reviewer_name' => 'Test',
        ])->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_authenticated_user_review_uses_their_account_name_and_is_linked_to_their_user_id(): void
    {
        $product = $this->product();
        $user    = User::factory()->create(['name' => 'Cliente Registrado']);

        $this->actingAs($user)->post(route('shop.product.reviews.store', $product->slug), [
            'rating'  => 3,
            'comment' => 'Está bien.',
        ])->assertRedirect();

        $this->assertDatabaseHas('product_reviews', [
            'product_id'    => $product->id,
            'user_id'       => $user->id,
            'reviewer_name' => 'Cliente Registrado',
            'rating'        => 3,
        ]);
    }

    public function test_authenticated_user_cannot_review_the_same_product_twice(): void
    {
        $product = $this->product();
        $user    = User::factory()->create();

        ProductReview::create([
            'product_id'    => $product->id,
            'user_id'       => $user->id,
            'reviewer_name' => $user->name,
            'rating'        => 4,
            'is_approved'   => true,
        ]);

        $this->actingAs($user)->post(route('shop.product.reviews.store', $product->slug), [
            'rating' => 5,
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('product_reviews', 1);
    }

    public function test_unapproved_reviews_do_not_show_on_the_public_product_page(): void
    {
        $product = $this->product();

        ProductReview::create([
            'product_id'    => $product->id,
            'reviewer_name' => 'Pendiente de moderar',
            'rating'        => 2,
            'comment'       => 'Comentario todavia no aprobado',
            'is_approved'   => false,
        ]);

        $response = $this->get(route('shop.product', $product->slug));

        $response->assertOk();
        $response->assertDontSee('Pendiente de moderar');
    }

    public function test_approved_reviews_show_on_the_public_product_page(): void
    {
        $product = $this->product();

        ProductReview::create([
            'product_id'    => $product->id,
            'reviewer_name' => 'Cliente Feliz',
            'rating'        => 5,
            'comment'       => 'Excelente calidad',
            'is_approved'   => true,
        ]);

        $response = $this->get(route('shop.product', $product->slug));

        $response->assertOk();
        $response->assertSee('Cliente Feliz');
        $response->assertSee('Excelente calidad');
    }

    // ── Moderación desde el admin ──────────────────────────────────────────

    private function actingAsRole(string $role): User
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_editor_can_approve_a_pending_review(): void
    {
        $this->actingAsRole('editor');
        $product = $this->product();

        $review = ProductReview::create([
            'product_id'    => $product->id,
            'reviewer_name' => 'Alguien',
            'rating'        => 4,
            'is_approved'   => false,
        ]);

        $this->patch(route('admin.reviews.approve', $review))->assertRedirect();

        $this->assertTrue($review->fresh()->is_approved);
    }

    public function test_editor_can_delete_a_review(): void
    {
        $this->actingAsRole('editor');
        $product = $this->product();

        $review = ProductReview::create([
            'product_id'    => $product->id,
            'reviewer_name' => 'Alguien',
            'rating'        => 1,
            'is_approved'   => false,
        ]);

        $this->delete(route('admin.reviews.destroy', $review))->assertRedirect();

        $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
    }

    public function test_vendedor_cannot_access_review_moderation(): void
    {
        $this->actingAsRole('vendedor');

        $this->get(route('admin.reviews.index'))->assertForbidden();
    }
}
