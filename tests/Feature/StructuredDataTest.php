<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructuredDataTest extends TestCase
{
    use RefreshDatabase;

    private function extractJsonLd(string $html, string $type): ?array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);
        foreach ($matches[1] as $json) {
            $data = json_decode(trim($json), true);
            if (($data['@type'] ?? null) === $type) {
                return $data;
            }
        }
        return null;
    }

    public function test_product_page_includes_valid_product_json_ld(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de cobre',
            'slug' => 'pulsera-de-cobre',
            'short_description' => 'Una pulsera artesanal.',
            'price' => 75000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $response = $this->get(route('shop.product', $product->slug));
        $response->assertOk();

        $data = $this->extractJsonLd($response->getContent(), 'Product');

        $this->assertNotNull($data, 'Debería haber un bloque JSON-LD de tipo Product.');
        $this->assertSame('Pulsera de cobre', $data['name']);
        $this->assertSame('75000', $data['offers']['price']);
        $this->assertSame('PYG', $data['offers']['priceCurrency']);
        $this->assertSame('https://schema.org/InStock', $data['offers']['availability']);
        $this->assertArrayNotHasKey('aggregateRating', $data);
    }

    public function test_out_of_stock_product_reports_out_of_stock_availability(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Anillo agotado',
            'slug' => 'anillo-agotado',
            'price' => 40000,
            'stock' => 0,
            'is_active' => true,
        ]);

        $response = $this->get(route('shop.product', $product->slug));
        $data = $this->extractJsonLd($response->getContent(), 'Product');

        $this->assertSame('https://schema.org/OutOfStock', $data['offers']['availability']);
    }

    public function test_product_with_reviews_includes_aggregate_rating(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Collar valorado',
            'slug' => 'collar-valorado',
            'price' => 60000,
            'stock' => 5,
            'is_active' => true,
            'rating_avg' => 4.5,
            'rating_count' => 12,
        ]);

        $response = $this->get(route('shop.product', $product->slug));
        $data = $this->extractJsonLd($response->getContent(), 'Product');

        $this->assertSame('4.5', $data['aggregateRating']['ratingValue']);
        $this->assertSame('12', $data['aggregateRating']['reviewCount']);
    }

    public function test_every_public_page_includes_organization_json_ld(): void
    {
        $response = $this->get(route('home'));
        $data = $this->extractJsonLd($response->getContent(), 'Organization');

        $this->assertNotNull($data);
        $this->assertSame('Cateura Accesorios', $data['name']);
        $this->assertSame(url('/'), $data['url']);
    }
}
