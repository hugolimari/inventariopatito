<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de Tarjeta Gráfica — hereda de Hardware.
 * Mapea la tabla `tarjetas_graficas` (tabla hija en Class Table Inheritance).
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
        private string $vram,
        bool $etiquetado = false,
        int $vida_util_meses = 36,
        string $estado = 'Llegada',
        int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    // ── Getter específico ───────────────────────────────────

    public function getVram(): string { return $this->vram; }

    // ── Setter específico ───────────────────────────────────

    public function setVram(string $vram): void { $this->vram = $vram; }

    // ── Polimorfismo ────────────────────────────────────────

    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->vram}";
    }
}
