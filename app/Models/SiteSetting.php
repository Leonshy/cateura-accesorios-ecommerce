<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key','value','group'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        // Si la clave ya existía en otro grupo (o el registro es nuevo y el
        // grupo cambia), hay que invalidar el caché de AMBOS grupos, no solo
        // el nuevo, para no dejar una copia vieja del grupo anterior.
        $previousGroup = static::where('key', $key)->value('group');

        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);

        Cache::forget("setting_{$key}");
        Cache::forget("settings_group_{$group}");
        if ($previousGroup && $previousGroup !== $group) {
            Cache::forget("settings_group_{$previousGroup}");
        }
    }

    public static function group(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }
}
