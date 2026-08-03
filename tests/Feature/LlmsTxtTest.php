<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LlmsTxtTest extends TestCase
{
    use RefreshDatabase;

    public function test_llms_txt_describes_the_site_and_links_key_pages(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('# Cateura Accesorios', false);
        $response->assertSee(route('shop.index'), false);
        $response->assertSee(route('sitemap'), false);
    }

    public function test_llms_txt_lists_active_categories(): void
    {
        Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);
        Category::create(['name' => 'Oculta', 'slug' => 'oculta', 'is_active' => false]);

        $response = $this->get('/llms.txt');

        $response->assertSee('Joyas');
        $response->assertDontSee('Oculta');
    }
}
