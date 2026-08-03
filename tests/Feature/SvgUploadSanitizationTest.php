<?php

namespace Tests\Feature;

use App\Models\MediaFile;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SvgUploadSanitizationTest extends TestCase
{
    use RefreshDatabase;

    private array $writtenFiles = [];

    protected function tearDown(): void
    {
        // processUpload() escribe directo con `copy()` a storage_path(), sin
        // pasar por la fachada Storage, así que Storage::fake() no lo
        // intercepta. Se limpian los archivos reales que quedaron en disco.
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

    private function fakeSvg(string $contents, string $name = 'imagen.svg'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'svgtest_') . '.svg';
        file_put_contents($path, $contents);

        return new UploadedFile($path, $name, 'image/svg+xml', null, true);
    }

    private function readStoredContents(MediaFile $media): string
    {
        $path = storage_path('app/public/' . $media->file_path);
        $this->writtenFiles[] = $path;

        return file_get_contents($path);
    }

    public function test_uploaded_svg_with_a_script_tag_is_stripped_before_saving(): void
    {
        $this->actingAsEditor();

        $malicious = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.cookie)</script><circle cx="50" cy="50" r="40"/></svg>';

        $response = $this->post(route('admin.media.upload'), [
            'files' => [$this->fakeSvg($malicious)],
        ]);

        $response->assertCreated();

        $media = MediaFile::first();
        $this->assertNotNull($media);
        $stored = $this->readStoredContents($media);

        $this->assertStringNotContainsString('<script', $stored);
        $this->assertStringContainsString('circle', $stored);
    }

    public function test_uploaded_svg_with_inline_event_handlers_is_stripped(): void
    {
        $this->actingAsEditor();

        $malicious = '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><rect width="10" height="10" onclick="alert(2)"/></svg>';

        $this->post(route('admin.media.upload'), [
            'files' => [$this->fakeSvg($malicious)],
        ])->assertCreated();

        $media = MediaFile::first();
        $stored = $this->readStoredContents($media);

        $this->assertStringNotContainsString('onload', $stored);
        $this->assertStringNotContainsString('onclick', $stored);
    }

    public function test_a_clean_svg_uploads_successfully_and_keeps_its_shape(): void
    {
        $this->actingAsEditor();

        $clean = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="red"/></svg>';

        $response = $this->post(route('admin.media.upload'), [
            'files' => [$this->fakeSvg($clean)],
        ]);

        $response->assertCreated();
        $media = MediaFile::first();
        $this->assertNotNull($media);
        $this->assertSame('image/svg+xml', $media->mime_type);

        $stored = $this->readStoredContents($media);
        $this->assertStringContainsString('circle', $stored);
        $this->assertStringContainsString('fill="red"', $stored);
    }

    public function test_an_empty_svg_file_is_rejected(): void
    {
        $this->actingAsEditor();

        $response = $this->post(route('admin.media.upload'), [
            'files' => [$this->fakeSvg('')],
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, MediaFile::count());
    }
}
