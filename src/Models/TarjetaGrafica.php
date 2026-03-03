<?php
declare(strict_types=1);

namespace App\Models;

class TarjetaGrafica extends Hardware
{
    public function __construct(
        ?int $id, string $marca, string $modelo, float $precio, int $stock, string $categoria,
        private string $vram,
        bool $etiquetado = false, int $vida_util_meses = 36, string $estado = 'Llegada', int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, 'TarjetaGrafica', $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    public function getVram(): string { return $this->vram; }
    public function setVram(string $vram): void { $this->vram = $vram; }

    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->vram}";
    }
}
