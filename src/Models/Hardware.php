<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Clase abstracta base para todos los componentes de hardware.
 * Demuestra: Abstracción, Encapsulamiento, Herencia (clase padre).
 * Mapea la tabla `hardware` (Class Table Inheritance).
 */
abstract class Hardware
{
    public function __construct(
        protected ?int $id,
        protected string $marca,
        protected string $modelo,
        protected float $precio,
        protected int $stock,
        protected string $categoria,
        protected bool $etiquetado = false,
        protected int $vida_util_meses = 36,
        protected string $estado = 'Llegada',
        protected int $usuario_id = 1
    ) {}

    // ── Getters ──────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }
    public function getMarca(): string { return $this->marca; }
    public function getModelo(): string { return $this->modelo; }
    public function getPrecio(): float { return $this->precio; }
    public function getStock(): int { return $this->stock; }
    public function getCategoria(): string { return $this->categoria; }
    public function getEtiquetado(): bool { return $this->etiquetado; }
    public function getVidaUtilMeses(): int { return $this->vida_util_meses; }
    public function getEstado(): string { return $this->estado; }
    public function getUsuarioId(): int { return $this->usuario_id; }

    // ── Setters ──────────────────────────────────────────────

    public function setId(int $id): void { $this->id = $id; }
    public function setMarca(string $marca): void { $this->marca = $marca; }
    public function setModelo(string $modelo): void { $this->modelo = $modelo; }
    public function setPrecio(float $precio): void { $this->precio = $precio; }
    public function setStock(int $stock): void { $this->stock = $stock; }
    public function setCategoria(string $categoria): void { $this->categoria = $categoria; }
    public function setEtiquetado(bool $etiquetado): void { $this->etiquetado = $etiquetado; }
    public function setVidaUtilMeses(int $meses): void { $this->vida_util_meses = $meses; }
    public function setEstado(string $estado): void { $this->estado = $estado; }
    public function setUsuarioId(int $id): void { $this->usuario_id = $id; }

    // ── Lógica de negocio ───────────────────────────────────

    /**
     * Verifica si el stock está en nivel crítico (menos de 3 unidades).
     */
    public function esStockCritico(): bool
    {
        return $this->stock < 3;
    }

    /**
     * Método abstracto — cada subclase lo implementa (Polimorfismo).
     */
    abstract public function obtenerDetallesTecnicos(): string;
}
