<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El grupo único "content" mezclaba los textos de la página de inicio, de
 * "Nuestras artesanas" y de "Nosotros" en un solo lugar. Se separan en tres
 * grupos según el prefijo de cada clave, para que cada página pública tenga
 * su propia pantalla de edición en el panel sin pisar los datos de las otras.
 */
return new class extends Migration
{
    public function up(): void
    {
        // El "_" del LIKE ya funciona como comodín de un solo carácter, así que
        // no hace falta escaparlo: "historia_%" matchea igual las claves reales
        // (p. ej. "historia_eyebrow") sin depender del carácter de escape de cada
        // motor de base de datos (SQLite no trata "\" como escape por defecto).
        DB::table('site_settings')->where('group', 'content')->where('key', 'like', 'historia_%')->update(['group' => 'content_home']);
        DB::table('site_settings')->where('group', 'content')->where('key', 'like', 'artisans_%')->update(['group' => 'content_artisans']);
        DB::table('site_settings')->where('group', 'content')->where('key', 'like', 'about_%')->update(['group' => 'content_about']);
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('group', ['content_home', 'content_artisans', 'content_about'])->update(['group' => 'content']);
    }
};
