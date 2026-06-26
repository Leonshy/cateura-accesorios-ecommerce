<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Subcategory extends Model
{
    use HasSlug;
    protected $fillable = ['category_id','name','slug','description','is_active','order'];
    protected $casts = ['is_active' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function products() { return $this->hasMany(Product::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
