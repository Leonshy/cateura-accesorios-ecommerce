<?php
namespace Database\Seeders;
use App\Models\Artisan;
use Illuminate\Database\Seeder;

class ArtisanSeeder extends Seeder
{
    public function run(): void
    {
        $artisans = [
            ['name' => 'María Elena Rojas', 'specialty' => 'Joyería en cobre', 'years_experience' => 12,
             'bio' => "María Elena nació y creció en el Bañado Sur de Asunción. A los 25 años descubrió que los fragmentos de cobre que los recicladores traían del vertedero podían transformarse en hermosas pulseras y collares.\n\nHoy, con más de una década de experiencia, María Elena es una de las artesanas más prolíficas de la asociación. Sus pulseras de cobre trenzado son reconocidas en toda la región por su delicadeza y acabado impecable."],
            ['name' => 'Carmen Giménez', 'specialty' => 'Macramé y textiles', 'years_experience' => 8,
             'bio' => "Carmen aprendió la técnica de macramé de su abuela, quien tejía hamacas y tapices en el interior del país. Al llegar a Asunción, combinó esa tradición con los materiales que encontraba en el barrio.\n\nSus piezas mezclan cuerdas naturales con elementos de metal reciclado, creando una estética única que combina lo rural y lo urbano."],
            ['name' => 'Rosa Benítez', 'specialty' => 'Pintura en materiales reciclados', 'years_experience' => 6,
             'bio' => "Rosa llegó a la asociación buscando una salida laboral después de perder su empleo. Nunca había pintado profesionalmente, pero tenía un talento natural que las coordinadoras del taller descubrieron enseguida.\n\nEspecializada en portavelas y marcos decorativos, Rosa transforma latas ordinarias en pequeñas obras de arte con motivos guaraní y florales."],
            ['name' => 'Ana Sosa', 'specialty' => 'Bisutería y aretes', 'years_experience' => 9,
             'bio' => "Ana es conocida en el barrio como 'la artista de los aretes'. Con una habilidad extraordinaria para trabajar con piezas pequeñas, crea diseños intricados que parecen imposibles de hacer a mano.\n\nSus aretes de lata pintada han sido exhibidos en ferias internacionales de artesanía y forman parte de colecciones privadas en varios países."],
            ['name' => 'Julia Palacios', 'specialty' => 'Bolsos y carteras tejidas', 'years_experience' => 15,
             'bio' => "Julia es la artesana más experimentada de la asociación. Llegó al taller cuando tenía apenas 20 años y no ha parado desde entonces. Maestra en el tejido ao poí, ha enseñado su técnica a decenas de compañeras.\n\nSus bolsos son reconocibles por la perfección de su tejido y la solidez de sus costuras. Julia insiste en que cada puntada debe ser tan resistente como bella."],
            ['name' => 'Ramona Villalba', 'specialty' => 'Sets y packaging artesanal', 'years_experience' => 5,
             'bio' => "Ramona se unió a la asociación hace cinco años y aportó algo que el taller necesitaba: el ojo para el diseño de producto completo. Además de crear bellos aretes y pulseras, Ramona diseña el empaque artesanal de todos los productos.\n\nSus cajas de regalo decoradas a mano con ñandutí son tan apreciadas como las joyas que contienen."],
        ];

        foreach ($artisans as $i => $a) {
            Artisan::firstOrCreate(['name' => $a['name']], array_merge($a, ['order' => $i + 1, 'is_active' => true]));
        }
    }
}
