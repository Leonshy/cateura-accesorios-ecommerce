<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('department')->nullable();
            $table->string('country', 5)->default('PY');
            $table->string('phone', 30)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customer_addresses'); }
};
