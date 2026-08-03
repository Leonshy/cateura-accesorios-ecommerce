<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LegalPage extends Model
{
    /**
     * Términos y privacidad son de aceptación obligatoria en el checkout,
     * así que nunca se pueden ocultar del sitio público.
     */
    public const ALWAYS_VISIBLE_KEYS = ['privacidad', 'terminos'];

    private const ACTIVE_MAP_CACHE_KEY = 'legal_pages_active_map';

    protected $fillable = ['key','title','content','is_active','meta_title','meta_description','updated_by_at'];
    protected $casts = ['updated_by_at' => 'datetime', 'is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::ACTIVE_MAP_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::ACTIVE_MAP_CACHE_KEY));
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function isToggleable(): bool
    {
        return !in_array($this->key, self::ALWAYS_VISIBLE_KEYS, true);
    }

    /**
     * El footer está en todas las páginas y necesita saber qué páginas legales
     * están activas para decidir qué enlaces mostrar — se cachea para no
     * repetir esta consulta en cada request.
     */
    public static function activeMapCached(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::ACTIVE_MAP_CACHE_KEY, 3600, function () {
            return static::pluck('is_active', 'key');
        });
    }
}
