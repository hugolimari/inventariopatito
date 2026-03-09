<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('receptor_id')->nullable()->constrained('users')->restrictOnDelete();
            
            $table->foreignId('activo_fijo_id')->nullable()->constrained('activos_fijos')->restrictOnDelete();
            $table->foreignId('lote_consumible_id')->nullable()->constrained('lotes_consumibles')->restrictOnDelete();
            
            $table->enum('tipo_movimiento', ['Ingreso', 'Check-out', 'Check-in', 'Baja', 'Venta', 'RMA']);
            $table->integer('cantidad_afectada');
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex_movimientos');
    }
};
