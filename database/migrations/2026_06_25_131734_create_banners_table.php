<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('image');
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('position')->default('home_hero');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('banners'); }
};
