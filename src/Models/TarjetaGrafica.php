<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de Tarjeta Gráfica — hereda de Hardware.
 * Demuestra: Herencia y Polimorfismo (implementa obtenerDetallesTecnicos).
 */
class TarjetaGrafica extends Hardware
{
    public function __construct(
        ?int $id,
        string $marca,
        string $modelo,
        float $precio,
        int $stock,
        string $categoria,
        private string $vram
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria);
    }

    // ── Getter específico ───────────────────────────────────

    public function getVram(): string { return $this->vram; }

    // ── Polimorfismo ────────────────────────────────────────

    /**
     * Retorna los detalles técnicos específicos de una tarjeta gráfica.
     */
    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->vram}";
    }
}
