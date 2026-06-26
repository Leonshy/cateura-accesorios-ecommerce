<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token', 64)->nullable()->index();
            // Customer
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_ci', 30)->nullable();
            // Address
            $table->string('address_line1')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_department')->nullable();
            $table->string('address_country', 5)->default('PY');
            $table->text('address_notes')->nullable();
            // Billing
            $table->string('billing_ruc')->nullable();
            $table->string('billing_name')->nullable();
            // Payment & Shipping
            $table->string('payment_method');
            $table->string('payment_status')->default('pendiente');
            $table->string('shipping_method')->default('coordinar');
            $table->unsignedBigInteger('shipping_cost')->default(0)->nullable();
            $table->string('transfer_receipt')->nullable();
            $table->string('bancard_process_id')->nullable();
            $table->string('pagopar_hash')->nullable();
            // Totals
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total');
            // Status
            $table->string('status')->default('pendiente');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
