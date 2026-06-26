<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    protected $fillable = ['key','title','content','meta_title','meta_description','updated_by_at'];
    protected $casts = ['updated_by_at' => 'datetime'];
}
