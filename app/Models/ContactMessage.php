<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name','email','phone','subject','message','is_read','reply','replied_at'];
    protected $casts = ['is_read' => 'boolean', 'replied_at' => 'datetime'];
    public function scopeUnread($q) { return $q->where('is_read', false); }
}
