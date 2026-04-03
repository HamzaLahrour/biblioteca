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
        Schema::create('espacios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tipo_espacio_id')->constrained('tipo_espacios')->onDelete('restrict');
            $table->string('nombre'); 
            $table->string('codigo')->nullable()->unique(); 
            $table->integer('capacidad');
            $table->string('ubicacion'); 
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('espacios');
    }
};
