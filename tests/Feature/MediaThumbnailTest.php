<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MediaFile;
use App\Models\Product;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MediaThumbnailTest extends TestCase
{
    use RefreshDatabase;

    private array $writtenFiles = [];

    protected function tearDown(): void
    {
        // processUpload() escribe directo con `copy()` a storage_path(), sin
        // pasar por la fachada Storage (mismo patrón que SvgUploadSanitizationTest).
        foreach ($this->writtenFiles as $path) {
            @unlink($path);
        }
        parent::tearDown();
    }

    private function actingAsEditor(): User
    {
        $editor = User::factory()->create();
        UserRole::create(['user_id' => $editor->id, 'role' => 'editor']);
        $this->actingAs($editor);
        return $editor;
    }

    /** Genera un JPEG real (no un mock) para que GD pueda procesarlo de verdad. */
    private function fakeJpeg(int $width = 2000, int $height = 1500, string $name = 'foto.jpg'): UploadedFile
    {
        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, 200, 120, 60);
        imagefill($img, 0, 0, $bg);

        $path = tempnam(sys_get_temp_dir(), 'jpgtest_') . '.jpg';
        imagejpeg($img, $path, 90);
        imagedestroy($img);

        return new UploadedFile($path, $name, 'image/jpeg', null, true);
    }

    private function storedPath(MediaFile $media): string
    {
        $path = storage_path('app/public/' . $media->file_path);
        $this->writtenFiles[] = $path;
        return $path;
    }

    private function storedThumbPath(MediaFile $media): string
    {
        $path = storage_path('app/public/' . $media->thumb_path);
        $this->writtenFiles[] = $path;
        return $path;
    }

    public function test_uploading_a_large_photo_generates_a_smaller_thumbnail(): void
    {
        $this->actingAsEditor();

        $this->post(route('admin.media.upload'), [
            'files' => [$this->fakeJpeg(2000, 1500)],
        ])->assertCreated();

        $media = MediaFile::first();
        $this->assertNotNull($media->thumb_path);

        [$fullWidth, $fullHeight] = getimagesize($this->storedPath($media));
        [$thumbWidth, $thumbHeight] = getimagesize($this->storedThumbPath($media));

        $this->assertSame(2000, $fullWidth);
        $this->assertLessThanOrEqual(600, $thumbWidth);
        $this->assertLessThanOrEqual(600, $thumbHeight);
        // Debe conservar la proporción (4:3 en este caso).
        $this->assertEqualsWithDelta($fullWidth / $fullHeight, $thumbWidth / $thumbHeight, 0.02);
    }

    public function test_a_small_photo_still_gets_a_thumbnail_capped_at_its_own_size(): void
    {
        $this->actingAsEditor();

        $this->post(route('admin.media.upload'), [
            'files' => [$this->fakeJpeg(300, 200)],
        ])->assertCreated();

        $media = MediaFile::first();
        $this->assertNotNull($media->thumb_path);
        $this->storedPath($media);

        [$thumbWidth, $thumbHeight] = getimagesize($this->storedThumbPath($media));
        $this->assertSame(300, $thumbWidth);
        $this->assertSame(200, $thumbHeight);
    }

    public function test_svg_uploads_do_not_get_a_thumbnail(): void
    {
        $this->actingAsEditor();
        $path = tempnam(sys_get_temp_dir(), 'svgtest_') . '.svg';
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="4"/></svg>');

        $this->post(route('admin.media.upload'), [
            'files' => [new UploadedFile($path, 'icono.svg', 'image/svg+xml', null, true)],
        ])->assertCreated();

        $media = MediaFile::first();
        $this->writtenFiles[] = storage_path('app/public/' . $media->file_path);
        $this->assertNull($media->thumb_path);
    }

    public function test_thumb_url_falls_back_to_file_url_when_there_is_no_thumbnail(): void
    {
        $media = MediaFile::create([
            'file_name' => 'legado.jpg',
            'file_path' => 'media/legado.jpg',
            'thumb_path' => null,
            'file_url' => 'https://ejemplo.com/media/legado.jpg',
            'mime_type' => 'image/jpeg',
        ]);

        $this->assertSame('https://ejemplo.com/media/legado.jpg', $media->thumb_url);
    }

    public function test_deleting_a_media_file_also_deletes_its_thumbnail_from_disk(): void
    {
        $this->actingAsEditor();
        $this->post(route('admin.media.upload'), ['files' => [$this->fakeJpeg()]])->assertCreated();

        $media = MediaFile::first();
        $thumbPath = storage_path('app/public/' . $media->thumb_path);
        $fullPath = storage_path('app/public/' . $media->file_path);
        $this->assertFileExists($thumbPath);

        $this->delete(route('admin.media.destroy', $media))->assertOk();

        $this->assertFileDoesNotExist($thumbPath);
        $this->assertFileDoesNotExist($fullPath);
    }

    // ── catalog_thumb_url() helper ──

    public function test_catalog_thumb_url_returns_the_thumbnail_when_it_exists(): void
    {
        $this->actingAsEditor();
        $this->post(route('admin.media.upload'), ['files' => [$this->fakeJpeg()]])->assertCreated();
        $media = MediaFile::first();
        $this->storedPath($media);
        $this->storedThumbPath($media);

        $result = catalog_thumb_url($media->file_path);

        $this->assertStringContainsString('-thumb.jpg', $result);
    }

    public function test_catalog_thumb_url_falls_back_to_full_image_for_legacy_paths_without_a_thumbnail(): void
    {
        // Simula una imagen de producto cargada por seeder/antes de este
        // cambio: existe el archivo "completo" pero nunca se generó miniatura.
        $result = catalog_thumb_url('products/legacy-product.jpg');

        $this->assertStringNotContainsString('-thumb', $result);
        $this->assertStringContainsString('legacy-product.jpg', $result);
    }

    public function test_catalog_thumb_url_leaves_external_urls_untouched(): void
    {
        $result = catalog_thumb_url('https://cdn.externo.com/foto.jpg');

        $this->assertSame('https://cdn.externo.com/foto.jpg', $result);
    }

    // ── Product::catalog_image ──

    public function test_product_catalog_image_uses_the_thumbnail_when_available(): void
    {
        $this->actingAsEditor();
        $this->post(route('admin.media.upload'), ['files' => [$this->fakeJpeg()]])->assertCreated();
        $media = MediaFile::first();
        $this->storedPath($media);
        $this->storedThumbPath($media);

        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-' . uniqid(),
            'price' => 50000,
            'stock' => 5,
            'is_active' => true,
            'image' => $media->file_path,
        ]);

        $this->assertStringContainsString('-thumb.jpg', $product->catalog_image);
        $this->assertStringNotContainsString('-thumb', $product->main_image);
    }

    public function test_product_without_an_image_uses_the_placeholder_for_both_accessors(): void
    {
        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera sin foto',
            'slug' => 'pulsera-sin-foto-' . uniqid(),
            'price' => 50000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->assertStringContainsString('placeholder-product.jpg', $product->catalog_image);
        $this->assertSame($product->main_image, $product->catalog_image);
    }

    public function test_product_card_on_the_shop_page_renders_the_catalog_thumbnail(): void
    {
        $this->actingAsEditor();
        $this->post(route('admin.media.upload'), ['files' => [$this->fakeJpeg()]])->assertCreated();
        $media = MediaFile::first();
        $this->storedPath($media);
        $this->storedThumbPath($media);

        $category = Category::create(['name' => 'Joyas', 'slug' => 'joyas-' . uniqid(), 'is_active' => true]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Pulsera de prueba',
            'slug' => 'pulsera-' . uniqid(),
            'price' => 50000,
            'stock' => 5,
            'is_active' => true,
            'image' => $media->file_path,
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertOk();
        $response->assertSee('-thumb.jpg', false);
    }
}
