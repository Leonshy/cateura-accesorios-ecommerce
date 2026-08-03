<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductRelatedQueriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewing_a_product_does_not_run_an_extra_query_per_related_product(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera principal',
            'slug' => 'pulsera-principal',
            'price' => 50000,
            'stock' => 5,
            'is_active' => true,
        ]);

        foreach (range(1, 4) as $i) {
            Product::create([
                'category_id' => $category->id,
                'name' => "Producto relacionado {$i}",
                'slug' => "producto-relacionado-{$i}",
                'price' => 30000,
                'stock' => 5,
                'is_active' => true,
            ]);
        }

        DB::enableQueryLog();
        $this->get(route('shop.product', $product->slug))->assertOk();
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $categoryQueries = collect($queries)->filter(
            fn ($q) => str_contains(strtolower($q['query']), 'from "categories"') || str_contains(strtolower($q['query']), 'from `categories`')
        );

        // 2 eager-loads (producto principal + relacionados) + 1 del menú de
        // navegación del header = 3 fijo. Sin el eager-load en $related,
        // esto escalaría a 6 (una consulta de categoría por cada uno de los
        // 4 productos relacionados mostrados en la tarjeta).
        $this->assertSame(3, $categoryQueries->count());
    }
}
