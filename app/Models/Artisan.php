<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Artisan extends Model
{
    use HasSlug;
    protected $fillable = ['name','slug','photo','specialty','bio','quote','years_experience','gallery','is_active','order'];
    protected $casts = ['gallery' => 'array', 'is_active' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? media_url($this->photo)
            : asset('assets/images/placeholder-artisan.jpg');
    }
}
