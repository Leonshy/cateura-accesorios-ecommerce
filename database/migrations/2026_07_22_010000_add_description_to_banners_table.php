<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->text('description')->nullable()->after('subtitle');
        });

        // El campo "subtitle" se usaba en realidad como el párrafo largo (descripción).
        // Se traslada ese contenido a "description" y se libera "subtitle" para su
        // rol real: el texto corto que aparece arriba del título (ej. "Colección artesanal").
        DB::statement('UPDATE banners SET description = subtitle');
        DB::table('banners')->update(['subtitle' => null]);
    }

    public function down(): void
    {
        DB::statement('UPDATE banners SET subtitle = description');

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
