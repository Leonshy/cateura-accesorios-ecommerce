<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaAdminController extends Controller
{
    // MIME real (finfo) → extensión normalizada. No se confía en la extensión del cliente.
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/gif'       => 'gif',
        'image/webp'      => 'webp',
        'image/svg+xml'   => 'svg',
        'application/pdf' => 'pdf',
    ];

    public function index(Request $request)
    {
        $query = MediaFile::query()->latest();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('mime_type', 'like', $type . '/%');
        }

        $files = $query->paginate(40)->withQueryString();
        $total = MediaFile::count();

        return view('admin.media.index', compact('files', 'total'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files'   => 'required|array|max:20',
            'files.*' => 'file|max:10240',
        ]);

        $uploaded = [];
        $errors   = [];

        foreach ($request->file('files') as $file) {
            try {
                $media      = $this->processUpload($file);
                $uploaded[] = $this->formatFile($media);
            } catch (\RuntimeException $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'uploaded' => $uploaded,
            'errors'   => $errors,
        ], $uploaded ? 201 : 422);
    }

    private function processUpload(\Illuminate\Http\UploadedFile $file): MediaFile
    {
        $realMime = $this->getRealMimeType($file->getRealPath());

        if (! array_key_exists($realMime, self::ALLOWED_MIME_TYPES)) {
            throw new \RuntimeException("Tipo de archivo no permitido ({$realMime}).");
        }

        $isImage = str_starts_with($realMime, 'image/') && $realMime !== 'image/svg+xml';
        $sourcePath = $file->getRealPath();

        if ($isImage && ! @getimagesize($sourcePath)) {
            throw new \RuntimeException('El archivo no es una imagen válida.');
        }

        $reencodedTmp = $isImage ? $this->stripExifAndReencode($sourcePath, $realMime) : null;

        $safeExt  = self::ALLOWED_MIME_TYPES[$realMime];
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = ($baseName ?: 'file') . '-' . time() . '-' . Str::random(6) . '.' . $safeExt;

        $destination = storage_path('app/public/media/' . $safeName);
        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }
        copy($reencodedTmp ?? $sourcePath, $destination);

        if ($reencodedTmp && file_exists($reencodedTmp)) {
            @unlink($reencodedTmp);
        }

        return MediaFile::create([
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => 'media/' . $safeName,
            'file_url'    => Storage::disk('public')->url('media/' . $safeName),
            'mime_type'   => $realMime,
            'file_size'   => filesize($destination),
            'alt_text'    => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => auth()->id(),
        ]);
    }

    private function getRealMimeType(string $path): string
    {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mime ?: 'application/octet-stream';
        }
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    private function stripExifAndReencode(string $path, string $mime): ?string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'upload_');

        try {
            $img = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/png'  => @imagecreatefrompng($path),
                'image/gif'  => @imagecreatefromgif($path),
                'image/webp' => @imagecreatefromwebp($path),
                default      => null,
            };

            if (! $img) {
                @unlink($tmp);
                return null;
            }

            // Preservar transparencia (PNG/WebP se aplanaban a fondo negro/blanco sin esto)
            if (in_array($mime, ['image/png', 'image/webp', 'image/gif'], true)) {
                imagealphablending($img, false);
                imagesavealpha($img, true);
            }

            match ($mime) {
                'image/jpeg' => imagejpeg($img, $tmp, 92),
                'image/png'  => imagepng($img, $tmp, 6),
                'image/gif'  => imagegif($img, $tmp),
                'image/webp' => imagewebp($img, $tmp, 90),
                default      => null,
            };

            imagedestroy($img);

            return $tmp;
        } catch (\Throwable) {
            @unlink($tmp);
            return null;
        }
    }

    public function updateAlt(Request $request, MediaFile $media)
    {
        $request->validate(['alt_text' => 'nullable|string|max:255']);
        $media->update(['alt_text' => $request->input('alt_text')]);
        return response()->json(['ok' => true]);
    }

    public function destroy(MediaFile $media)
    {
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        $media->delete();
        return response()->json(['ok' => true]);
    }

    // Endpoint JSON usado por el componente <x-admin.media-picker>
    public function picker(Request $request)
    {
        $query = MediaFile::query()->latest();

        if ($q = $request->input('q')) {
            $query->where(function ($qb) use ($q) {
                $qb->where('file_name', 'like', "%{$q}%")
                   ->orWhere('alt_text', 'like', "%{$q}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('mime_type', 'like', $type . '/%');
        }

        $page      = max(1, (int) $request->input('page', 1));
        $paginated = $query->paginate(40, ['*'], 'page', $page);

        return response()->json([
            'files'     => collect($paginated->items())->map(fn ($f) => $this->formatFile($f))->values(),
            'has_more'  => $paginated->hasMorePages(),
            'next_page' => $paginated->hasMorePages() ? $page + 1 : null,
            'total'     => $paginated->total(),
        ]);
    }

    private function formatFile(MediaFile $f): array
    {
        return [
            'id'         => $f->id,
            'file_name'  => $f->file_name,
            'file_url'   => $f->file_url,
            'mime_type'  => $f->mime_type,
            'file_size'  => $f->file_size,
            'alt_text'   => $f->alt_text,
            'is_image'   => str_starts_with($f->mime_type ?? '', 'image/'),
            'size_label' => $f->size_label,
            'created_at' => $f->created_at?->diffForHumans(),
        ];
    }
}
