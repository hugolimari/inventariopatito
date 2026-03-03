<?php
declare(strict_types=1);

namespace App\Models;

class PlacaBase extends Hardware
{
    public function __construct(
        ?int $id, string $marca, string $modelo, float $precio, int $stock, string $categoria,
        private string $socket,
        private string $formato,
        bool $etiquetado = false, int $vida_util_meses = 36, string $estado = 'Llegada', int $usuario_id = 1
    ) {
        parent::__construct($id, $marca, $modelo, $precio, $stock, $categoria, 'PlacaBase', $etiquetado, $vida_util_meses, $estado, $usuario_id);
    }

    public function getSocket(): string { return $this->socket; }
    public function getFormato(): string { return $this->formato; }
    public function setSocket(string $v): void { $this->socket = $v; }
    public function setFormato(string $v): void { $this->formato = $v; }

    public function obtenerDetallesTecnicos(): string
    {
        return "Socket {$this->socket} — {$this->formato}";
    }
}
