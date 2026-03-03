<?php
declare(strict_types=1);

namespace App\Models;

class MemoriaRAM extends Hardware
{
    public function __construct(
        ?int $id, string $marca, string $modelo, float $precio, int $stock, string $categoria,
        private string $capacidad,
        private string $tipo,
        private string $velocidad,
        bool $etiquetado = false, int $vida_util_meses = 36, string $estado = 'Llegada', int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, 'MemoriaRAM', $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    public function getCapacidad(): string { return $this->capacidad; }
    public function getTipo(): string { return $this->tipo; }
    public function getVelocidad(): string { return $this->velocidad; }
    public function setCapacidad(string $v): void { $this->capacidad = $v; }
    public function setTipo(string $v): void { $this->tipo = $v; }
    public function setVelocidad(string $v): void { $this->velocidad = $v; }

    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->capacidad} {$this->tipo} a {$this->velocidad}";
    }
}
