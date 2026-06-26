<?php
namespace Database\Seeders;
use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['title' => 'Arte que transforma vidas', 'subtitle' => 'Accesorios artesanales hechos con amor por mujeres del Bañado Sur', 'cta_label' => 'Ver colección', 'cta_url' => '/tienda', 'order' => 1, 'image' => 'banners/banner-1.jpg'],
            ['title' => 'Nueva colección 2026', 'subtitle' => 'Sonidos del Bañado — Inspirada en la música del reciclaje', 'cta_label' => 'Descubrir', 'cta_url' => '/tienda', 'order' => 2, 'image' => 'banners/banner-2.jpg'],
            ['title' => 'Comprás y transformás', 'subtitle' => 'Cada compra apoya directamente a artesanas paraguayas', 'cta_label' => 'Conocer artesanas', 'cta_url' => '/artesanas', 'order' => 3, 'image' => 'banners/banner-3.jpg'],
        ];

        foreach ($banners as $b) {
            Banner::firstOrCreate(['title' => $b['title']], array_merge($b, ['is_active' => true, 'position' => 'home_hero']));
        }
    }
}
