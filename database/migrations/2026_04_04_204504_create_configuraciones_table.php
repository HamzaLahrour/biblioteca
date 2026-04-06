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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('seccion');        // 'horario', 'reservas', 'prestamos'...
            $table->string('clave')->unique(); // 'hora_apertura', 'duracion_minima'...
            $table->string('valor');           // '09:00', '30', 'true'...
            $table->string('tipo');            // 'time', 'integer', 'boolean', 'string'
            $table->string('etiqueta');        // 'Hora de apertura' (lo que ve el admin)
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
