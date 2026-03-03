<?php
declare(strict_types=1);

namespace App\Models;

class Procesador extends Hardware
{
    public function __construct(
        ?int $id, string $marca, string $modelo, float $precio, int $stock, string $categoria,
        private int $nucleos,
        private string $frecuencia,
        bool $etiquetado = false, int $vida_util_meses = 36, string $estado = 'Llegada', int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, 'Procesador', $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    public function getNucleos(): int { return $this->nucleos; }
    public function getFrecuencia(): string { return $this->frecuencia; }
    public function setNucleos(int $nucleos): void { $this->nucleos = $nucleos; }
    public function setFrecuencia(string $frecuencia): void { $this->frecuencia = $frecuencia; }

    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->nucleos} Núcleos a {$this->frecuencia}";
    }
}
