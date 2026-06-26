<?php
namespace Database\Seeders;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['key' => 'retiro_local',   'name' => 'Retiro en local (Bañado Sur)',        'cost' => 0,      'is_active' => true,  'order' => 1],
            ['key' => 'envio_asuncion', 'name' => 'Envío Asunción y Gran Asunción',       'cost' => 15000,  'is_active' => true,  'order' => 2],
            ['key' => 'envio_interior', 'name' => 'Envío interior del país',              'cost' => 35000,  'is_active' => true,  'order' => 3],
        ];

        foreach ($methods as $m) {
            ShippingMethod::firstOrCreate(['key' => $m['key']], $m);
        }
    }
}
