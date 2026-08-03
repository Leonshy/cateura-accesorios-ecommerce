<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'is_active', 'subscribed_at', 'confirmation_token', 'confirmed_at'];
    protected $casts = ['is_active' => 'boolean', 'subscribed_at' => 'datetime', 'confirmed_at' => 'datetime'];

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    public function scopePendingConfirmation($query)
    {
        return $query->whereNull('confirmed_at');
    }
}
