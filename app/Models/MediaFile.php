<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $fillable = ['file_name', 'file_path', 'file_url', 'mime_type', 'file_size', 'alt_text', 'uploaded_by'];

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
}
