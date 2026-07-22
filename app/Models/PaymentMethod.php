<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['key','name','description','credentials','is_active','sandbox','order'];
    protected $casts = ['credentials' => 'array', 'is_active' => 'boolean', 'sandbox' => 'boolean'];
    public function scopeActive($q) { return $q->where('is_active', true); }

    public static function getProvider(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}
