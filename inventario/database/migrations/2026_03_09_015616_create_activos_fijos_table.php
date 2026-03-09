<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_fijos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_id')->constrained('catalogos')->restrictOnDelete();
            $table->string('numero_serie', 100)->unique();
            
            $table->enum('estado', ['En Almacén', 'Asignado', 'Dado de Baja', 'Vendido', 'Defectuoso'])->default('En Almacén');
            
            // Relaciones con la tabla users
            $table->foreignId('asignado_a')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('creado_por')->constrained('users')->restrictOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activos_fijos');
    }
};
