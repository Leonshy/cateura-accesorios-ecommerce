<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['user_id','phone','avatar_url','birthdate','document_type','document_number'];
    protected $casts = ['birthdate' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
}
