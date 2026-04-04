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
        Schema::create('sanciones', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Corregido a foreignId y user_id para que coincida con la tabla users
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');

            $table->text('razon');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // (Opcional) Si quieres que el admin pueda perdonar la sanción antes de tiempo
            $table->date('fecha_levantamiento_manual')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};
