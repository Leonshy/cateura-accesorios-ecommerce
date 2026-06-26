<?php
namespace Database\Seeders;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['key' => 'transferencia', 'name' => 'Transferencia bancaria', 'credentials' => [], 'is_active' => true, 'sandbox' => false, 'order' => 1],
            ['key' => 'pagopar',       'name' => 'Pagopar',  'credentials' => ['api_key' => '', 'public_key' => ''], 'is_active' => false, 'sandbox' => true, 'order' => 2],
            ['key' => 'bancard',       'name' => 'Bancard',  'credentials' => ['private_key' => '', 'public_key' => ''], 'is_active' => false, 'sandbox' => true, 'order' => 3],
        ];

        foreach ($methods as $m) {
            PaymentMethod::firstOrCreate(['key' => $m['key']], $m);
        }
    }
}
