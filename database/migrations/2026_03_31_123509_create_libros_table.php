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
        Schema::create('libros', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
        
            $table->foreignUuid('categoria_id')->constrained('categorias')->onDelete('restrict');
        
            $table->string('titulo');
            $table->string('autor');
            $table->string('isbn')->unique()->nullable();
            $table->string('editorial')->nullable();
            $table->integer('anio_publicacion')->nullable();
        
            // ÚNICA FUENTE DE VERDAD
            $table->integer('copias_totales')->default(1);
        
            $table->string('portada')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
