<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Clase abstracta base para todos los componentes de hardware.
 * Demuestra: Abstracción, Encapsulamiento, Herencia (clase padre).
 */
abstract class Hardware
{
    public function __construct(
        protected ?int $id,
        protected string $marca,
        protected string $modelo,
        protected float $precio,
        protected int $stock,
        protected string $categoria
    ) {}

    // ── Getters ──────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }
    public function getMarca(): string { return $this->marca; }
    public function getModelo(): string { return $this->modelo; }
    public function getPrecio(): float { return $this->precio; }
    public function getStock(): int { return $this->stock; }
    public function getCategoria(): string { return $this->categoria; }

    // ── Setter para ID (usado por el repositorio) ───────────

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // ── Lógica de negocio ───────────────────────────────────

    /**
     * Verifica si el stock está en nivel crítico (menos de 3 unidades).
     * Usado en HU 10 para resaltar filas con alerta visual.
     */
    public function esStockCritico(): bool
    {
        return $this->stock < 3;
    }

    /**
     * Método abstracto — cada subclase lo implementa de forma diferente.
     * Esto es POLIMORFISMO: la misma llamada produce distinto resultado
     * según el tipo concreto del objeto.
     */
    abstract public function obtenerDetallesTecnicos(): string;
}
