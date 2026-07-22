<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_values', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('text');
            $table->string('icon')->default('leaf');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Migrar los 3 valores fijos que vivían en site_settings (about_valor1/2/3_*), si existen.
        $settings = DB::table('site_settings')
            ->whereIn('key', [
                'about_valor1_title', 'about_valor1_text',
                'about_valor2_title', 'about_valor2_text',
                'about_valor3_title', 'about_valor3_text',
            ])
            ->pluck('value', 'key');

        $defaults = [
            1 => ['icon' => 'leaf', 'title' => 'Sostenibilidad', 'text' => 'Trabajamos con materiales reciclados para crear productos que cuidan el medio ambiente y reducen los residuos en nuestra comunidad.'],
            2 => ['icon' => 'handshake', 'title' => 'Comercio justo', 'text' => 'Cada venta impacta directamente en la economía de las artesanas y sus familias, garantizando un precio justo por su trabajo.'],
            3 => ['icon' => 'spark', 'title' => 'Arte y tradición', 'text' => 'Fusionamos técnicas artesanales tradicionales con diseños contemporáneos para crear piezas únicas con identidad paraguaya.'],
        ];

        foreach ($defaults as $i => $default) {
            DB::table('about_values')->insert([
                'title'      => $settings->get("about_valor{$i}_title") ?: $default['title'],
                'text'       => $settings->get("about_valor{$i}_text") ?: $default['text'],
                'icon'       => $default['icon'],
                'order'      => $i,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('about_values');
    }
};
