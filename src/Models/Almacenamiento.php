<?php
declare(strict_types=1);

namespace App\Models;

class Almacenamiento extends Hardware
{
    public function __construct(
        ?int $id, string $marca, string $modelo, float $precio, int $stock, string $categoria,
        private string $capacidad,
        private string $tipo,
        private ?string $velocidad_lectura = null,
        bool $etiquetado = false, int $vida_util_meses = 36, string $estado = 'Llegada', int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, 'Almacenamiento', $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    public function getCapacidad(): string { return $this->capacidad; }
    public function getTipoAlmacenamiento(): string { return $this->tipo; }
    public function getVelocidadLectura(): ?string { return $this->velocidad_lectura; }
    public function setCapacidad(string $v): void { $this->capacidad = $v; }
    public function setTipoAlmacenamiento(string $v): void { $this->tipo = $v; }
    public function setVelocidadLectura(?string $v): void { $this->velocidad_lectura = $v; }

    public function obtenerDetallesTecnicos(): string
    {
        $det = "{$this->capacidad} {$this->tipo}";
        if ($this->velocidad_lectura) {
            $det .= " ({$this->velocidad_lectura})";
        }
        return $det;
    }
}
