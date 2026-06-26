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
            ['key' => 'contact_email',    'value' => 'hola@cateuraaccesorios.com',                   'group' => 'general'],
            ['key' => 'contact_phone',    'value' => '+595 981 234 567',                              'group' => 'general'],
            ['key' => 'contact_address',  'value' => 'Bañado Sur, Asunción, Paraguay',               'group' => 'general'],
            ['key' => 'instagram_url',    'value' => 'https://instagram.com/cateuraaccesorios',       'group' => 'general'],
            ['key' => 'facebook_url',     'value' => 'https://facebook.com/cateuraaccesorios',        'group' => 'general'],
            ['key' => 'whatsapp_number',  'value' => '595981234567',                                  'group' => 'general'],
            ['key' => 'whatsapp_message', 'value' => 'Hola! Me interesa un producto de Cateura Accesorios 😊', 'group' => 'general'],
            // bank transfer
            ['key' => 'bank_name',        'value' => 'Banco Nacional de Fomento (BNF)',               'group' => 'payment'],
            ['key' => 'bank_account',     'value' => '0000-00000-0',                                  'group' => 'payment'],
            ['key' => 'bank_titular',     'value' => 'Asoc. Mujeres Unidas del Bañado Sur',           'group' => 'payment'],
            ['key' => 'bank_ci',          'value' => '0.000.000',                                     'group' => 'payment'],
        ];

        foreach ($settings as $s) {
            SiteSetting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
