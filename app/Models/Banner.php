<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title','subtitle','description','image','cta_label','cta_url','position','is_active','order'];
    protected $casts = ['is_active' => 'boolean'];
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function getImageUrlAttribute(): string { return media_url($this->image); }
}
