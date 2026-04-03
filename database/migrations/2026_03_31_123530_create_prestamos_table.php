<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->uuid('id')->primary();
        
            // Relaciones
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('libro_id')->constrained('libros')->onDelete('cascade');
        
            // Control de fechas
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_prevista');
            $table->date('fecha_devolucion_real')->nullable();
        
            // Estado: activo, devuelto, retrasado...
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
