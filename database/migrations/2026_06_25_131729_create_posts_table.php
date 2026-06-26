<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->enum('type', ['noticia', 'evento'])->default('noticia');
            $table->enum('status', ['publicado', 'borrador'])->default('borrador');
            $table->dateTime('event_date')->nullable();
            $table->string('event_location')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('meta_index')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('posts'); }
};
