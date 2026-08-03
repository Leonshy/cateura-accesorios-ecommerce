<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubcategoryOrderingTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    private function makeCategoryWithSubcategories(array $names): array
    {
        $category = Category::create(['name' => 'Joyería Artesanal', 'slug' => 'joyeria-artesanal', 'is_active' => true]);
        $subs = [];
        foreach ($names as $i => $name) {
            $subs[] = Subcategory::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'is_active' => true,
                'order' => $i,
            ]);
        }
        return [$category, $subs];
    }

    private function namesInOrder(Category $category): array
    {
        return Subcategory::where('category_id', $category->id)->ordered()->pluck('name')->all();
    }

    public function test_new_subcategory_is_appended_at_the_end_regardless_of_existing_order_values(): void
    {
        $this->actingAsAdmin();
        [$category] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares']);

        $this->post(route('admin.subcategories.store', $category), ['name' => 'Aretes'])
            ->assertRedirect();

        $this->assertEquals(['Pulseras', 'Collares', 'Aretes'], $this->namesInOrder($category));
    }

    public function test_new_subcategory_is_appended_last_even_when_existing_rows_share_the_same_order_value(): void
    {
        // Antes del fix, las subcategorías nuevas se creaban siempre con order=0,
        // lo que las insertaba primero (o generaba empates) en vez de al final.
        $this->actingAsAdmin();
        $category = Category::create(['name' => 'Bolsos', 'slug' => 'bolsos', 'is_active' => true]);
        Subcategory::create(['category_id' => $category->id, 'name' => 'Mochilas', 'slug' => 'mochilas', 'is_active' => true, 'order' => 0]);
        Subcategory::create(['category_id' => $category->id, 'name' => 'Carteras', 'slug' => 'carteras', 'is_active' => true, 'order' => 0]);

        $this->post(route('admin.subcategories.store', $category), ['name' => 'Riñoneras'])
            ->assertRedirect();

        $this->assertEquals('Riñoneras', $this->namesInOrder($category)[2]);
    }

    public function test_move_up_swaps_with_the_previous_sibling(): void
    {
        $this->actingAsAdmin();
        [$category, $subs] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares', 'Aretes']);
        [$pulseras, $collares, $aretes] = $subs;

        $this->post(route('admin.subcategories.move-up', $aretes))->assertRedirect();

        $this->assertEquals(['Pulseras', 'Aretes', 'Collares'], $this->namesInOrder($category));
    }

    public function test_move_down_swaps_with_the_next_sibling(): void
    {
        $this->actingAsAdmin();
        [$category, $subs] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares', 'Aretes']);
        [$pulseras, $collares, $aretes] = $subs;

        $this->post(route('admin.subcategories.move-down', $pulseras))->assertRedirect();

        $this->assertEquals(['Collares', 'Pulseras', 'Aretes'], $this->namesInOrder($category));
    }

    public function test_move_up_on_the_first_item_is_a_no_op(): void
    {
        $this->actingAsAdmin();
        [$category, $subs] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares']);
        [$pulseras] = $subs;

        $this->post(route('admin.subcategories.move-up', $pulseras))->assertRedirect();

        $this->assertEquals(['Pulseras', 'Collares'], $this->namesInOrder($category));
    }

    public function test_move_down_on_the_last_item_is_a_no_op(): void
    {
        $this->actingAsAdmin();
        [$category, $subs] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares']);
        [, $collares] = $subs;

        $this->post(route('admin.subcategories.move-down', $collares))->assertRedirect();

        $this->assertEquals(['Pulseras', 'Collares'], $this->namesInOrder($category));
    }

    public function test_moving_normalizes_duplicated_order_values_from_before_the_fix(): void
    {
        $this->actingAsAdmin();
        $category = Category::create(['name' => 'Accesorios', 'slug' => 'accesorios', 'is_active' => true]);
        // Simula el estado heredado: varias filas con el mismo valor de `order`.
        $a = Subcategory::create(['category_id' => $category->id, 'name' => 'A', 'slug' => 'a', 'is_active' => true, 'order' => 0]);
        $b = Subcategory::create(['category_id' => $category->id, 'name' => 'B', 'slug' => 'b', 'is_active' => true, 'order' => 0]);
        $c = Subcategory::create(['category_id' => $category->id, 'name' => 'C', 'slug' => 'c', 'is_active' => true, 'order' => 0]);

        $this->post(route('admin.subcategories.move-down', $a))->assertRedirect();

        $orders = Subcategory::where('category_id', $category->id)->ordered()->pluck('order', 'name');
        // Tras renumerar, los valores de orden deben quedar 0,1,2 sin repetirse.
        $this->assertEquals([0, 1, 2], $orders->values()->sort()->values()->all());
        $this->assertEquals(['B', 'A', 'C'], $this->namesInOrder($category));
    }

    public function test_moving_a_subcategory_from_one_category_does_not_affect_another_categorys_order(): void
    {
        $this->actingAsAdmin();
        [$categoryOne, $subsOne] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares']);
        [$categoryTwo, $subsTwo] = $this->makeCategoryWithSubcategories(['Sombreros', 'Bufandas']);

        $this->post(route('admin.subcategories.move-down', $subsOne[0]))->assertRedirect();

        $this->assertEquals(['Sombreros', 'Bufandas'], $this->namesInOrder($categoryTwo));
    }

    public function test_update_no_longer_accepts_a_manual_order_field(): void
    {
        $this->actingAsAdmin();
        [$category, $subs] = $this->makeCategoryWithSubcategories(['Pulseras', 'Collares']);
        [$pulseras] = $subs;

        $this->patch(route('admin.subcategories.update', $pulseras), [
            'name' => 'Pulseras',
            'order' => 99,
            'is_active' => 1,
        ])->assertRedirect();

        // El orden se gestiona solo con las flechas; un `order` enviado a mano se ignora.
        $this->assertEquals(0, $pulseras->fresh()->order);
    }
}
