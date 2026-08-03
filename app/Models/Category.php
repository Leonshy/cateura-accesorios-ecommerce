<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug;

    private const ACTIVE_CACHE_KEY = 'categories_active_ordered';

    protected $fillable = ['name','slug','description','image','meta_title','meta_description','is_active','order'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::ACTIVE_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::ACTIVE_CACHE_KEY));
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? media_url($this->image) : asset('assets/images/placeholder-product.jpg');
    }

    /**
     * Header, footer y el menú móvil están en TODAS las páginas y cada uno
     * necesitaba esta misma lista de categorías activas — sin esto, era la
     * misma consulta 3 veces por request. Se cachea una sola vez y se
     * invalida sola al crear/editar/borrar una categoría (ver booted()).
     */
    public static function activeOrderedCached()
    {
        return Cache::remember(self::ACTIVE_CACHE_KEY, 3600, function () {
            return static::active()->orderBy('order')->get();
        });
    }
}
