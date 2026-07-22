<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaUsage extends Model
{
    protected $fillable = ['media_id', 'entity_type', 'entity_id', 'field_name'];

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class, 'media_id');
    }
}
