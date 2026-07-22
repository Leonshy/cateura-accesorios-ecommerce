<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasSlug;
    protected $fillable = [
        'title','slug','excerpt','content','image','gallery','type','status',
        'event_date','event_location','cta_label','cta_url',
        'meta_title','meta_description','meta_index','published_at',
    ];
    protected $casts = [
        'gallery' => 'array',
        'meta_index' => 'boolean',
        'event_date' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function scopePublished($q) { return $q->where('status', 'publicado'); }
    public function scopeNoticias($q) { return $q->where('type', 'noticia'); }
    public function scopeEventos($q) { return $q->where('type', 'evento'); }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? media_url($this->image) : null;
    }
}
