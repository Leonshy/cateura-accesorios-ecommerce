<?php
namespace Database\Seeders;
use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['key' => 'privacidad',   'title' => 'Política de privacidad',   'content' => '<p>En Cateura Accesorios, nos comprometemos a proteger tu privacidad. Los datos personales que recopilamos son utilizados únicamente para procesar tus pedidos y mejorar tu experiencia de compra. Nunca compartimos tu información con terceros sin tu consentimiento. Podés solicitar la eliminación de tus datos en cualquier momento escribiéndonos a hola@cateuraaccesorios.com.</p>'],
            ['key' => 'terminos',     'title' => 'Términos y condiciones',   'content' => '<p>Al realizar una compra en Cateura Accesorios, aceptás que todos los productos son artesanales y pueden presentar variaciones menores en color, textura o dimensiones, lo que forma parte del valor y la autenticidad de cada pieza. Los precios están expresados en Guaraníes (PYG) e incluyen IVA.</p>'],
            ['key' => 'compra',       'title' => 'Políticas de compra',      'content' => '<p>Aceptamos pagos por transferencia bancaria y métodos electrónicos habilitados. Los pedidos se procesan una vez confirmado el pago. Podés cancelar tu pedido dentro de las 24 horas de realizado, siempre que no haya sido enviado aún.</p>'],
            ['key' => 'envio',        'title' => 'Políticas de envío',       'content' => '<p>Realizamos envíos a todo el Paraguay. Los tiempos de entrega son de 3-5 días hábiles para Asunción y Gran Asunción, y 5-8 días hábiles para el interior del país. El retiro en local (Bañado Sur, Asunción) no tiene costo adicional.</p>'],
            ['key' => 'devoluciones', 'title' => 'Cambios y devoluciones',   'content' => '<p>Si tu producto llega dañado o presenta algún defecto de fabricación, contactanos dentro de los 7 días de recibido y te enviaremos un reemplazo sin costo. Para cambios por talla o preferencia de estilo, el cliente abona el costo del envío de retorno.</p>'],
        ];

        foreach ($pages as $p) {
            LegalPage::firstOrCreate(['key' => $p['key']], $p);
        }
    }
}
