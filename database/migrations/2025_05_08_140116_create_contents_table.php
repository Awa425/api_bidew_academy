<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['text', 'video', 'pdf', 'link','jpeg','jpg']);
            $table->longText('data')->nullable(); // texte brut ou HTML
            $table->string('file_path')->nullable(); // pour les fichiers
            $table->string('external_url')->nullable(); // pour les vidéos externes ou liens
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
