<?php
namespace Database\Seeders;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // general
            ['key' => 'site_name',        'value' => 'Cateura Accesorios',                          'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Accesorios artesanales del Bañado Sur',        'group' => 'general'],
            ['key' => 'footer_description', 'value' => 'Accesorios, piezas decorativas y prendas creadas por artesanas del Bañado Sur a partir de materiales reciclados.', 'group' => 'general'],
            ['key' => 'contact_email',    'value' => 'asomujeresunidas22@gmail.com',                 'group' => 'general'],
            ['key' => 'contact_phone',    'value' => '0981 877 315',                                  'group' => 'general'],
            ['key' => 'contact_address',  'value' => "Taller Productivo 43 Proyectadas c/ Capitán Figari\nBarrio San Cayetano, Bañado Sur\nAsunción, Paraguay", 'group' => 'general'],
            ['key' => 'instagram_url',    'value' => 'https://www.instagram.com/cateurapy',           'group' => 'general'],
            // No hay una cuenta de Facebook confirmada en ningún lado del sitio: se deja vacía
            // a propósito para que el ícono no se muestre hasta que se cargue el enlace real.
            ['key' => 'facebook_url',     'value' => '',                                              'group' => 'general'],
            ['key' => 'whatsapp_number',  'value' => '595981877315',                                  'group' => 'general'],
            ['key' => 'whatsapp_message', 'value' => 'Hola! Me interesa un producto de Cateura Accesorios 😊', 'group' => 'general'],
            // Los datos bancarios para transferencia se cargan y editan en la
            // pantalla de Integraciones, dentro de las credenciales del método
            // de pago "transferencia" (ver PaymentMethodSeeder), no acá.
        ];

        foreach ($settings as $s) {
            SiteSetting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
