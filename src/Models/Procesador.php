<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de Procesador — hereda de Hardware.
 * Demuestra: Herencia y Polimorfismo (implementa obtenerDetallesTecnicos).
 */
class Procesador extends Hardware
{
    public function __construct(
        ?int $id,
        string $marca,
        string $modelo,
        float $precio,
        int $stock,
        string $categoria,
        private int $nucleos,
        private string $frecuencia
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria);
    }

    // ── Getters específicos ─────────────────────────────────

    public function getNucleos(): int { return $this->nucleos; }
    public function getFrecuencia(): string { return $this->frecuencia; }

    // ── Polimorfismo ────────────────────────────────────────

    /**
     * Retorna los detalles técnicos específicos de un procesador.
     */
    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->nucleos} Núcleos a {$this->frecuencia}";
    }
}
