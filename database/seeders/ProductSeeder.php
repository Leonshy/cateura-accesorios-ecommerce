<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $joyas   = Category::where('name', 'like', 'Joyas%')->first();
        $hogar   = Category::where('name', 'like', 'Hogar%')->first();
        $textil  = Category::where('name', 'like', 'Textil%')->first();

        $products = [
            // Joyas
            ['name' => 'Pulsera de Cobre Reciclado', 'price' => 85000, 'original_price' => 110000, 'stock' => 15, 'category' => $joyas, 'is_featured' => true, 'is_new' => true,
             'description' => 'Elegante pulsera artesanal elaborada con cobre reciclado de instrumentos musicales del vertedero de Cateura. Cada pieza es única e irrepetible, un testimonio de resiliencia y creatividad.', 'colors' => ['Cobre', 'Plateado']],
            ['name' => 'Collar Espiral Artesanal', 'price' => 120000, 'stock' => 8, 'category' => $joyas, 'is_featured' => true,
             'description' => 'Collar de espirales entrelazadas hecho a mano con alambre de cobre recuperado. Diseño contemporáneo con alma tradicional paraguaya.', 'colors' => ['Cobre oxidado', 'Dorado']],
            ['name' => 'Aretes Flor de Lata', 'price' => 55000, 'stock' => 20, 'category' => $joyas, 'is_new' => true,
             'description' => 'Delicados aretes en forma de flor elaborados con latas recicladas y técnica de repujado artesanal. Livianos y coloridos.', 'colors' => ['Multicolor', 'Plateado']],
            ['name' => 'Set Pulseras Trenzadas', 'price' => 95000, 'original_price' => 130000, 'stock' => 12, 'category' => $joyas, 'is_featured' => true,
             'description' => 'Set de 3 pulseras trenzadas con hilos de colores y cierres artesanales. Perfectas para regalar o combinar en conjunto.', 'colors' => ['Natural', 'Colorado', 'Azul']],
            ['name' => 'Anillo Ajustable Cobre', 'price' => 45000, 'stock' => 25, 'category' => $joyas,
             'description' => 'Anillo ajustable de cobre con textura martillada. Talla única, se adapta a cualquier dedo. Acabado natural envejecido.'],
            ['name' => 'Pulsera Macramé y Cobre', 'price' => 72000, 'stock' => 18, 'category' => $joyas, 'is_new' => true,
             'description' => 'Pulsera con técnica de macramé combinada con elementos de cobre reciclado. Cierre ajustable con nudo corredizo.', 'colors' => ['Natural', 'Terracota']],
            // Hogar
            ['name' => 'Portavelas Lata Pintada', 'price' => 65000, 'stock' => 10, 'category' => $hogar, 'is_featured' => true, 'is_new' => true,
             'description' => 'Portavelas elaborado con latas recicladas pintadas a mano con motivos paraguayo-guaraní. Crea una atmósfera cálida y artesanal en cualquier espacio.'],
            ['name' => 'Marco de Fotos Reciclado', 'price' => 88000, 'stock' => 7, 'category' => $hogar,
             'description' => 'Marco para fotos de 10×15 cm elaborado con materiales reciclados: madera recuperada y detalles de cobre. Pieza única.'],
            ['name' => 'Jardinera Decorativa', 'price' => 110000, 'original_price' => 140000, 'stock' => 5, 'category' => $hogar, 'is_featured' => true,
             'description' => 'Jardinera decorativa mediana hecha con materiales reciclados. Ideal para plantas pequeñas o como adorno de escritorio.'],
            ['name' => 'Móvil Decorativo Musical', 'price' => 135000, 'stock' => 4, 'category' => $hogar, 'is_new' => true,
             'description' => 'Móvil decorativo inspirado en los instrumentos de Cateura. Cuerdas, llaves y latas que danzan con el viento creando música visual.'],
            // Textil
            ['name' => 'Bolso Tejido Ao Poí', 'price' => 185000, 'original_price' => 220000, 'stock' => 6, 'category' => $textil, 'is_featured' => true,
             'description' => 'Bolso mediano tejido con técnica tradicional ao poí paraguayo. Asas de cuero ecológico. Cierre con broche artesanal. Diseño exclusivo Cateura.', 'colors' => ['Natural', 'Terracota', 'Azul índigo']],
            ['name' => 'Cartera Monedero Ñandutí', 'price' => 95000, 'stock' => 9, 'category' => $textil, 'is_new' => true,
             'description' => 'Cartera-monedero con bordado de ñandutí en la tapa. Interior de tela reciclada con forro. Cierre de cremallera y argolla para llavero.', 'colors' => ['Rojo', 'Azul', 'Negro']],
            ['name' => 'Mochila Artesanal Mediana', 'price' => 245000, 'original_price' => 290000, 'stock' => 3, 'category' => $textil, 'is_featured' => true,
             'description' => 'Mochila de tela tejida con capacidad de 15 litros. Correas ajustables acolchadas. Bolsillo frontal con cierre. Hecha completamente a mano.', 'colors' => ['Natural', 'Colorado']],
            ['name' => 'Billetera de Tela Bordada', 'price' => 78000, 'stock' => 11, 'category' => $textil,
             'description' => 'Billetera bifold de tela con bordado artesanal guaraní. 4 tarjeteros, 2 bolsillos para billetes. Cierre con broche a presión.', 'colors' => ['Multicolor sobre negro', 'Multicolor sobre natural']],
            ['name' => 'Cinto Trenzado Artesanal', 'price' => 68000, 'stock' => 13, 'category' => $textil, 'is_new' => true,
             'description' => 'Cinto de cuero y tela trenzados a mano. Hebilla artesanal de cobre reciclado. Disponible en varias tallas.', 'colors' => ['Marrón/Cobre', 'Negro/Cobre']],
        ];

        foreach ($products as $data) {
            $colors = $data['colors'] ?? [];
            unset($data['colors']);
            $category = $data['category'];
            unset($data['category']);

            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'category_id'  => $category?->id,
                    'is_active'    => true,
                    'is_featured'  => $data['is_featured'] ?? false,
                    'is_new'       => $data['is_new'] ?? false,
                    'rating_avg'   => rand(40, 50) / 10,
                    'rating_count' => rand(3, 28),
                    'short_description' => substr($data['description'], 0, 120) . '...',
                ])
            );

            foreach ($colors as $color) {
                $product->colors()->firstOrCreate(['name' => $color]);
            }
        }
    }
}
