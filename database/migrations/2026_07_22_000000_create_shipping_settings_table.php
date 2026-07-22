<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->string('id')->primary()->default('default');
            $table->boolean('free_shipping_enabled')->default(true);
            $table->integer('free_shipping_min_amount')->default(500000);
            $table->boolean('store_pickup_enabled')->default(true);
            $table->boolean('envio_propio_enabled')->default(true);
            $table->json('zones')->default('[]');
            $table->boolean('aex_enabled')->default(false);
            $table->string('aex_api_user')->nullable();
            $table->string('aex_api_password')->nullable();
            $table->string('aex_environment')->default('sandbox');
            $table->boolean('aex_is_validated')->default(false);
            $table->string('aex_webhook_url')->nullable();
            $table->timestamps();
        });

        DB::table('shipping_settings')->insert([
            'id' => 'default',
            'free_shipping_enabled' => true,
            'free_shipping_min_amount' => 500000,
            'store_pickup_enabled' => true,
            'envio_propio_enabled' => true,
            'zones' => '[]',
            'aex_enabled' => false,
            'aex_environment' => 'sandbox',
            'aex_is_validated' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::dropIfExists('shipping_methods');
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
