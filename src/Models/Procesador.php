<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de Procesador — hereda de Hardware.
 * Mapea la tabla `procesadores` (tabla hija en Class Table Inheritance).
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
        private string $frecuencia,
        bool $etiquetado = false,
        int $vida_util_meses = 36,
        string $estado = 'Llegada',
        int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    // ── Getters específicos ─────────────────────────────────

    public function getNucleos(): int { return $this->nucleos; }
    public function getFrecuencia(): string { return $this->frecuencia; }

    // ── Setters específicos ─────────────────────────────────

    public function setNucleos(int $nucleos): void { $this->nucleos = $nucleos; }
    public function setFrecuencia(string $frecuencia): void { $this->frecuencia = $frecuencia; }

    // ── Polimorfismo ────────────────────────────────────────

    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->nucleos} Núcleos a {$this->frecuencia}";
    }
}
