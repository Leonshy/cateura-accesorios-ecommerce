<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Joyas y Bisutería', 'description' => 'Pulseras, collares, aretes y anillos artesanales elaborados con materiales reciclados.',
                'order' => 1, 'image' => 'categories/cat-joyas-y-bisuteria.jpg',
                'subcats' => ['Pulseras', 'Collares', 'Aretes', 'Anillos', 'Sets y Kits'],
            ],
            [
                'name' => 'Hogar y Decoración', 'description' => 'Objetos únicos para decorar tu hogar con identidad artesanal paraguaya.',
                'order' => 2, 'image' => 'categories/cat-hogar-y-decoracion.jpg',
                'subcats' => ['Portavelas', 'Marcos', 'Jarrones', 'Adornos'],
            ],
            [
                'name' => 'Textil y Accesorios', 'description' => 'Bolsos, carteras y accesorios textiles tejidos a mano con técnicas tradicionales.',
                'order' => 3, 'image' => 'categories/cat-textil-y-accesorios.jpg',
                'subcats' => ['Bolsos', 'Carteras', 'Mochilas', 'Accesorios textiles'],
            ],
        ];

        foreach ($categories as $data) {
            $subcats = $data['subcats'];
            unset($data['subcats']);
            $cat = Category::updateOrCreate(['name' => $data['name']], array_merge($data, ['is_active' => true]));
            foreach ($subcats as $i => $sub) {
                Subcategory::firstOrCreate(['name' => $sub, 'category_id' => $cat->id], ['order' => $i + 1, 'is_active' => true]);
            }
        }
    }
}
