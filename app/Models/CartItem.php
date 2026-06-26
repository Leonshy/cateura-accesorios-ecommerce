<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id','product_id','color','quantity','unit_price'];

    public function cart() { return $this->belongsTo(Cart::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function getSubtotalAttribute(): int
    {
        return $this->unit_price * $this->quantity;
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Gs. ' . number_format($this->subtotal, 0, ',', '.');
    }
}
