<?php
declare(strict_types=1);

namespace App\Models;

class FuentePoder extends Hardware
{
    public function __construct(
        ?int $id, string $marca, string $modelo, float $precio, int $stock, string $categoria,
        private string $potencia,
        private string $certificacion,
        bool $etiquetado = false, int $vida_util_meses = 36, string $estado = 'Llegada', int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, 'FuentePoder', $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    public function getPotencia(): string { return $this->potencia; }
    public function getCertificacion(): string { return $this->certificacion; }
    public function setPotencia(string $v): void { $this->potencia = $v; }
    public function setCertificacion(string $v): void { $this->certificacion = $v; }

    public function obtenerDetallesTecnicos(): string
    {
        return "{$this->potencia} — {$this->certificacion}";
    }
}
