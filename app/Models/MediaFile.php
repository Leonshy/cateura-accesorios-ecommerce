<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    protected $fillable = ['file_name', 'file_path', 'thumb_path', 'file_url', 'mime_type', 'file_size', 'alt_text', 'uploaded_by'];

    public function usages()
    {
        return $this->hasMany(MediaUsage::class, 'media_id');
    }

    public function getSizeLabelAttribute(): string
    {
        $bytes = $this->file_size;
        if (! $bytes) return '—';
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }

    /**
     * Miniatura para grillas/listados (catálogo, biblioteca). Si el archivo
     * no tiene una generada (imágenes subidas antes de este cambio, o que no
     * son rasterizadas, como SVG/PDF), cae de vuelta a la imagen original.
     */
    public function getThumbUrlAttribute(): string
    {
        return $this->thumb_path ? Storage::disk('public')->url($this->thumb_path) : $this->file_url;
    }
}
