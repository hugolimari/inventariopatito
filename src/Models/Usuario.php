<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de Usuario.
 * Mapea la tabla `usuarios`.
 */
class Usuario
{
    public function __construct(
        private ?int $id,
        private string $nombre_completo,
        private string $username,
        private string $password_hash,
        private string $rol = 'almacenero'
    ) {}

    // ── Getters ──────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }
    public function getNombreCompleto(): string { return $this->nombre_completo; }
    public function getUsername(): string { return $this->username; }
    public function getPasswordHash(): string { return $this->password_hash; }
    public function getRol(): string { return $this->rol; }

    // ── Setters ──────────────────────────────────────────────

    public function setId(int $id): void { $this->id = $id; }
    public function setNombreCompleto(string $nombre): void { $this->nombre_completo = $nombre; }
    public function setUsername(string $username): void { $this->username = $username; }
    public function setPasswordHash(string $hash): void { $this->password_hash = $hash; }
    public function setRol(string $rol): void { $this->rol = $rol; }

    // ── Lógica de negocio ───────────────────────────────────

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }
}
