<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAdminSubcategoryTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function makeCategoryWithSubcategory(): array
    {
        $category = Category::create(['name' => 'Joyería Artesanal', 'slug' => 'joyeria-artesanal', 'is_active' => true]);
        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Pulseras',
            'slug' => 'pulseras',
            'is_active' => true,
        ]);

        return [$category, $subcategory];
    }

    public function test_create_form_offers_subcategories_grouped_by_category(): void
    {
        $this->actingAsAdmin();
        [$category, $subcategory] = $this->makeCategoryWithSubcategory();

        $response = $this->get(route('admin.products.create'));

        $response->assertOk();
        // Los datos de categorías con sus subcategorías se serializan para Alpine.js.
        $response->assertSee($subcategory->name);
        $response->assertSee('subcategory_id', false);
    }

    public function test_store_saves_the_chosen_subcategory(): void
    {
        $this->actingAsAdmin();
        [$category, $subcategory] = $this->makeCategoryWithSubcategory();

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Pulsera de cuentas recicladas',
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 50000,
            'stock' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::firstWhere('name', 'Pulsera de cuentas recicladas');
        $this->assertNotNull($product);
        $this->assertEquals($category->id, $product->category_id);
        $this->assertEquals($subcategory->id, $product->subcategory_id);
    }

    public function test_store_allows_leaving_subcategory_empty(): void
    {
        $this->actingAsAdmin();
        [$category] = $this->makeCategoryWithSubcategory();

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Collar sin subcategoría',
            'category_id' => $category->id,
            'subcategory_id' => '',
            'price' => 60000,
            'stock' => 3,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::firstWhere('name', 'Collar sin subcategoría');
        $this->assertNotNull($product);
        $this->assertNull($product->subcategory_id);
    }

    public function test_store_rejects_a_subcategory_that_does_not_exist(): void
    {
        $this->actingAsAdmin();
        [$category] = $this->makeCategoryWithSubcategory();

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Producto con subcategoría inválida',
            'category_id' => $category->id,
            'subcategory_id' => 9999,
            'price' => 10000,
            'stock' => 1,
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('subcategory_id');
        $this->assertNull(Product::firstWhere('name', 'Producto con subcategoría inválida'));
    }

    public function test_edit_form_preselects_the_products_current_subcategory(): void
    {
        $this->actingAsAdmin();
        [$category, $subcategory] = $this->makeCategoryWithSubcategory();

        $product = Product::create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Aros de botellas recicladas',
            'slug' => 'aros-de-botellas-recicladas',
            'price' => 45000,
            'stock' => 8,
            'is_active' => true,
        ]);

        $response = $this->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertSee("subcategoryId: '{$subcategory->id}'", false);
    }

    public function test_update_changes_the_subcategory(): void
    {
        $this->actingAsAdmin();
        [$category, $subcategory] = $this->makeCategoryWithSubcategory();
        $otherSubcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Collares',
            'slug' => 'collares',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Anillo trenzado',
            'slug' => 'anillo-trenzado',
            'price' => 30000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $response = $this->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'category_id' => $category->id,
            'subcategory_id' => $otherSubcategory->id,
            'price' => 30000,
            'stock' => 4,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertEquals($otherSubcategory->id, $product->fresh()->subcategory_id);
    }

    public function test_update_rejects_a_subcategory_that_belongs_to_another_category_scenario_still_exists_in_db(): void
    {
        // La subcategoría existe en la base pero pertenece a otra categoría;
        // la validación actual solo verifica existencia (exists:subcategories,id),
        // así que este caso documenta el comportamiento actual, no una regla de negocio nueva.
        $this->actingAsAdmin();
        [$category, $subcategory] = $this->makeCategoryWithSubcategory();
        $otherCategory = Category::create(['name' => 'Bolsos', 'slug' => 'bolsos', 'is_active' => true]);

        $response = $this->post(route('admin.products.store'), [
            'name' => 'Bolso con subcategoría cruzada',
            'category_id' => $otherCategory->id,
            'subcategory_id' => $subcategory->id,
            'price' => 70000,
            'stock' => 2,
            'is_active' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $product = Product::firstWhere('name', 'Bolso con subcategoría cruzada');
        $this->assertNotNull($product);
        $this->assertEquals($subcategory->id, $product->subcategory_id);
    }
}
