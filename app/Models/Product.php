<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = [
        'category_id','subcategory_id','name','slug','short_description','description',
        'price','original_price','stock','sku','image','is_active','is_featured','is_new',
        'rating_avg','rating_count','meta_title','meta_description','og_image','meta_index',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'meta_index' => 'boolean',
        'rating_avg' => 'float',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function subcategory() { return $this->belongsTo(Subcategory::class); }
    public function images() { return $this->hasMany(ProductImage::class)->orderBy('order'); }
    public function colors() { return $this->hasMany(ProductColor::class); }
    public function reviews() { return $this->hasMany(ProductReview::class)->where('is_approved', true); }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
    public function scopeNew($q) { return $q->where('is_new', true); }
    public function scopeInStock($q) { return $q->where('stock', '>', 0); }

    public function getFormattedPriceAttribute(): string
    {
        return 'Gs. ' . number_format($this->price, 0, ',', '.');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return (int) round((1 - $this->price / $this->original_price) * 100);
        }
        return null;
    }

    public function getMainImageAttribute(): string
    {
        return $this->image
            ? media_url($this->image)
            : asset('assets/images/placeholder-product.jpg');
    }
}
