<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isVendedor(): bool
    {
        return $this->hasRole('vendedor') || $this->isAdmin();
    }

    public function isEditor(): bool
    {
        return $this->hasRole('editor') || $this->isAdmin();
    }

    public function canAccessAdmin(): bool
    {
        return $this->isAdmin() || $this->isVendedor() || $this->isEditor();
    }

    public function getHighestRole(): string
    {
        if ($this->isAdmin()) return 'admin';
        if ($this->hasRole('vendedor')) return 'vendedor';
        if ($this->hasRole('editor')) return 'editor';
        return 'cliente';
    }
}
