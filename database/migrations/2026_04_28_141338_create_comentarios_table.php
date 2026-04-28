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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ID del comentario

            // Relaciones con UUID
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('libro_id')->constrained()->onDelete('cascade');

            $table->integer('estrellas');
            $table->text('contenido')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
