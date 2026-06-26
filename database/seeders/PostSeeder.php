<?php
namespace Database\Seeders;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            ['title' => 'Cateura Accesorios en la Feria de las Artes de Asunción',
             'type' => 'evento', 'status' => 'publicado',
             'excerpt' => 'Vení a visitarnos en el stand de Cateura Accesorios en la 15ª Feria de las Artes de Asunción.',
             'content' => '<p>Estamos felices de anunciar nuestra participación en la Feria de las Artes de Asunción, uno de los eventos culturales más importantes del país.</p><p>Encontrarás toda nuestra colección 2026, así como piezas exclusivas que solo estarán disponibles durante la feria. También podrás conocer a nuestras artesanas y ver el proceso de creación en vivo.</p>',
             'published_at' => now()->subDays(5)],
            ['title' => 'Nueva colección: Sonidos del Bañado',
             'type' => 'noticia', 'status' => 'publicado',
             'excerpt' => 'Presentamos nuestra colección más personal, inspirada en la música que nació del reciclaje.',
             'content' => '<p>Con mucha emoción presentamos "Sonidos del Bañado", nuestra colección más personal hasta la fecha. Cada pieza de esta colección está inspirada en los instrumentos musicales del mundialmente famoso Reciclado de Cateura.</p><p>Claves de guitarra convertidas en aretes, cuerdas de violín transformadas en pulseras de macramé, resonadores de cajón en portavasos: la música literalmente habita cada accesorio.</p>',
             'published_at' => now()->subDays(12)],
            ['title' => 'Capacitación en nuevas técnicas de joyería',
             'type' => 'noticia', 'status' => 'publicado',
             'excerpt' => 'Nuestras artesanas completaron un taller de técnicas avanzadas en joyería artesanal con certificación.',
             'content' => '<p>Durante tres semanas, las integrantes de la Asociación Mujeres Unidas del Bañado Sur participaron de un taller intensivo de joyería artesanal con técnicas de soldadura, texturizado y acabados profesionales.</p><p>La capacitación fue facilitada por la fundación Artes y Oficios del Paraguay y financiada por un programa de cooperación internacional. Los nuevos conocimientos ya se están aplicando en nuestra colección 2026.</p>',
             'published_at' => now()->subDays(20)],
        ];

        foreach ($posts as $p) {
            Post::firstOrCreate(['title' => $p['title']], $p);
        }
    }
}
