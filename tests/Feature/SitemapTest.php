<?php

namespace Tests\Feature;

use App\Models\Artisan;
use App\Models\Category;
use App\Models\LegalPage;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_includes_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $response->assertSee(route('home'), false);
        $response->assertSee(route('shop.index'), false);
    }

    public function test_sitemap_includes_only_active_products(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true]);

        $active = Product::create([
            'category_id' => $category->id, 'name' => 'Producto activo', 'slug' => 'producto-activo',
            'price' => 10000, 'stock' => 1, 'is_active' => true,
        ]);
        $inactive = Product::create([
            'category_id' => $category->id, 'name' => 'Producto inactivo', 'slug' => 'producto-inactivo',
            'price' => 10000, 'stock' => 1, 'is_active' => false,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertSee(route('shop.product', $active->slug), false);
        $response->assertDontSee(route('shop.product', $inactive->slug), false);
    }

    public function test_sitemap_includes_only_active_artisans(): void
    {
        $visible = Artisan::create(['name' => 'Artesana visible', 'slug' => 'artesana-visible', 'bio' => 'x', 'is_active' => true, 'order' => 1]);
        $hidden = Artisan::create(['name' => 'Artesana oculta', 'slug' => 'artesana-oculta', 'bio' => 'x', 'is_active' => false, 'order' => 2]);

        $response = $this->get('/sitemap.xml');

        $response->assertSee(route('artisans.show', $visible->slug), false);
        $response->assertDontSee(route('artisans.show', $hidden->slug), false);
    }

    public function test_sitemap_includes_only_published_posts(): void
    {
        $published = Post::create(['title' => 'Noticia publicada', 'slug' => 'noticia-publicada', 'type' => 'noticia', 'status' => 'publicado']);
        $draft = Post::create(['title' => 'Noticia borrador', 'slug' => 'noticia-borrador', 'type' => 'noticia', 'status' => 'borrador']);

        $response = $this->get('/sitemap.xml');

        $response->assertSee(route('posts.show', $published->slug), false);
        $response->assertDontSee(route('posts.show', $draft->slug), false);
    }

    public function test_sitemap_excludes_a_post_marked_as_noindex(): void
    {
        $post = Post::create([
            'title' => 'Noticia sin indexar', 'slug' => 'noticia-sin-indexar', 'type' => 'noticia',
            'status' => 'publicado', 'meta_index' => false,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertDontSee(route('posts.show', $post->slug), false);
    }

    public function test_sitemap_only_includes_active_legal_pages(): void
    {
        LegalPage::create(['key' => 'compra', 'title' => 'Política de compra', 'content' => 'x', 'is_active' => true]);

        $response = $this->get('/sitemap.xml');

        $response->assertSee(route('legal.compra'), false);
    }

    public function test_robots_txt_points_to_the_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertSee(route('sitemap'), false);
    }
}
