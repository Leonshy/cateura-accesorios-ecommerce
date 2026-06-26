<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('artisans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->string('specialty')->nullable();
            $table->text('bio');
            $table->string('quote')->nullable();
            $table->unsignedInteger('years_experience')->default(0);
            $table->json('gallery')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('artisans'); }
};
