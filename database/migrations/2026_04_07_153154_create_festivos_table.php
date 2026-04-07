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
        Schema::create('festivos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            //Unique para que el admin no meta el mismo día dos veces por error
            $table->date('fecha')->unique();
            //Por ejemplo "Día de la Hispanidad"
            $table->string('motivo'); // Ej: "Día de la Hispanidad"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festivos');
    }
};
