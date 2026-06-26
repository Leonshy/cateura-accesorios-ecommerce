<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('avatar_url')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('document_type', 10)->nullable();
            $table->string('document_number', 30)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('profiles'); }
};
