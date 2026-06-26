<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = ['product_id','user_id','reviewer_name','rating','comment','is_approved'];
    protected $casts = ['is_approved' => 'boolean'];
    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function scopeApproved($q) { return $q->where('is_approved', true); }
}
