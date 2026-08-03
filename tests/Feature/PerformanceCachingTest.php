<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LegalPage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceCachingTest extends TestCase
{
    use RefreshDatabase;

    // ── SiteSetting::group() ──

    public function test_group_hits_the_database_only_once_across_repeated_calls(): void
    {
        SiteSetting::set('site_name', 'Cateura', 'general');

        DB::enableQueryLog();
        SiteSetting::group('general');
        SiteSetting::group('general');
        SiteSetting::group('general');
        $queries = collect(DB::getQueryLog())->filter(fn ($q) => str_contains($q['query'], 'site_settings'));

        $this->assertCount(1, $queries, 'group() debería golpear la base una sola vez, el resto debe venir de caché.');
    }

    public function test_group_reflects_a_change_made_through_set_immediately(): void
    {
        SiteSetting::set('site_name', 'Original', 'general');
        $this->assertSame('Original', SiteSetting::group('general')['site_name']);

        SiteSetting::set('site_name', 'Actualizado', 'general');

        $this->assertSame('Actualizado', SiteSetting::group('general')['site_name']);
    }

    public function test_moving_a_key_to_a_different_group_invalidates_both_group_caches(): void
    {
        SiteSetting::set('custom_key', 'valor', 'general');
        SiteSetting::group('general'); // deja el grupo "general" en caché

        SiteSetting::set('custom_key', 'valor', 'integrations'); // mismo key, grupo nuevo

        $this->assertArrayNotHasKey('custom_key', SiteSetting::group('general'));
        $this->assertSame('valor', SiteSetting::group('integrations')['custom_key']);
    }

    // ── Category::activeOrderedCached() ──

    public function test_active_ordered_cached_hits_the_database_only_once(): void
    {
        Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true, 'order' => 1]);
        Category::create(['name' => 'Hogar', 'slug' => 'hogar', 'is_active' => true, 'order' => 2]);

        DB::enableQueryLog();
        Category::activeOrderedCached();
        Category::activeOrderedCached();
        Category::activeOrderedCached();
        $queries = collect(DB::getQueryLog())->filter(fn ($q) => str_contains($q['query'], 'categories'));

        $this->assertCount(1, $queries);
    }

    public function test_creating_a_category_invalidates_the_cache(): void
    {
        Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true, 'order' => 1]);
        $this->assertCount(1, Category::activeOrderedCached());

        Category::create(['name' => 'Hogar', 'slug' => 'hogar', 'is_active' => true, 'order' => 2]);

        $this->assertCount(2, Category::activeOrderedCached());
    }

    public function test_deactivating_a_category_removes_it_from_the_cached_list(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true, 'order' => 1]);
        $this->assertCount(1, Category::activeOrderedCached());

        $category->update(['is_active' => false]);

        $this->assertCount(0, Category::activeOrderedCached());
    }

    public function test_deleting_a_category_removes_it_from_the_cached_list(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true, 'order' => 1]);
        $this->assertCount(1, Category::activeOrderedCached());

        $category->delete();

        $this->assertCount(0, Category::activeOrderedCached());
    }

    public function test_header_footer_and_mobile_menu_share_the_same_cached_category_query(): void
    {
        Category::create(['name' => 'Joyas', 'slug' => 'joyas', 'is_active' => true, 'order' => 1]);

        DB::enableQueryLog();
        $this->get(route('home'))->assertOk();
        $categoryQueries = collect(DB::getQueryLog())->filter(fn ($q) => str_contains($q['query'], '"categories"') || str_contains($q['query'], '`categories`'));

        // Antes del fix, header+footer+mobile-menu+HomeController hacían la
        // misma consulta 4 veces en una sola carga de home.
        $this->assertCount(1, $categoryQueries, 'La home no debería repetir la consulta de categorías activas.');
    }

    // ── LegalPage::activeMapCached() ──

    public function test_active_map_cached_hits_the_database_only_once(): void
    {
        LegalPage::create(['key' => 'envio', 'title' => 'Envío', 'content' => '<p>x</p>', 'is_active' => true]);

        DB::enableQueryLog();
        LegalPage::activeMapCached();
        LegalPage::activeMapCached();
        $queries = collect(DB::getQueryLog())->filter(fn ($q) => str_contains($q['query'], 'legal_pages'));

        $this->assertCount(1, $queries);
    }

    public function test_deactivating_a_legal_page_is_reflected_immediately_in_the_cached_map(): void
    {
        $page = LegalPage::create(['key' => 'envio', 'title' => 'Envío', 'content' => '<p>x</p>', 'is_active' => true]);
        $this->assertTrue(LegalPage::activeMapCached()['envio']);

        $page->update(['is_active' => false]);

        $this->assertFalse(LegalPage::activeMapCached()['envio']);
    }
}
