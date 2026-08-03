<?php
namespace Database\Seeders;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['key' => 'transferencia', 'name' => 'Transferencia bancaria', 'credentials' => [
                'bank_name'     => 'Banco Nacional de Fomento (BNF)',
                'bank_account'  => '0000-00000-0',
                'bank_titular'  => 'Asoc. Mujeres Unidas del Bañado Sur',
                'bank_ci'       => '0.000.000',
            ], 'is_active' => true, 'sandbox' => false, 'order' => 1],
            ['key' => 'pagopar',       'name' => 'Pagopar',  'credentials' => ['public_key' => '', 'private_key' => ''], 'is_active' => false, 'sandbox' => true, 'order' => 2],
            ['key' => 'bancard',       'name' => 'Bancard',  'credentials' => ['private_key' => '', 'public_key' => ''], 'is_active' => false, 'sandbox' => true, 'order' => 3],
        ];

        foreach ($methods as $m) {
            PaymentMethod::firstOrCreate(['key' => $m['key']], $m);
        }
    }
}
