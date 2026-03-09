<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 50); // CPU, GPU, RAM
            $table->string('marca', 50);
            $table->string('modelo', 100);
            $table->enum('tipo_registro', ['Serializado', 'Consumible']);
            $table->decimal('precio', 10, 2); // Para calcular el total del inventario
            $table->timestamps();
            $table->softDeletes(); // Crea la columna 'deleted_at' (Dar de baja)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
