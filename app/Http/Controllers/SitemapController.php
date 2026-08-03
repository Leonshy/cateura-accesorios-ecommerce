<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\Category;
use App\Models\LegalPage;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        $urls->push(['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0']);
        $urls->push(['loc' => route('shop.index'), 'changefreq' => 'daily', 'priority' => '0.9']);
        $urls->push(['loc' => route('artisans.index'), 'changefreq' => 'weekly', 'priority' => '0.6']);
        $urls->push(['loc' => route('posts.index'), 'changefreq' => 'weekly', 'priority' => '0.6']);
        $urls->push(['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.5']);
        $urls->push(['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.4']);

        Product::active()->select('slug', 'updated_at')->orderBy('updated_at', 'desc')->get()->each(
            fn ($product) => $urls->push([
                'loc' => route('shop.product', $product->slug),
                'lastmod' => $product->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ])
        );

        Artisan::active()->select('slug', 'updated_at')->get()->each(
            fn ($artisan) => $urls->push([
                'loc' => route('artisans.show', $artisan->slug),
                'lastmod' => $artisan->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ])
        );

        Post::published()->where(function ($q) {
            $q->whereNull('meta_index')->orWhere('meta_index', true);
        })->select('slug', 'updated_at')->get()->each(
            fn ($post) => $urls->push([
                'loc' => route('posts.show', $post->slug),
                'lastmod' => $post->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ])
        );

        $legalRoutes = [
            'privacidad' => 'legal.privacidad',
            'terminos' => 'legal.terminos',
            'compra' => 'legal.compra',
            'envio' => 'legal.envio',
            'devoluciones' => 'legal.devoluciones',
        ];
        LegalPage::whereIn('key', array_keys($legalRoutes))->where('is_active', true)->get()->each(
            fn ($page) => $urls->push([
                'loc' => route($legalRoutes[$page->key]),
                'lastmod' => $page->updated_at?->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ])
        );

        Category::active()->get()->each(
            fn ($category) => $urls->push([
                'loc' => route('shop.index', ['categoria' => $category->slug]),
                'lastmod' => $category->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ])
        );

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
