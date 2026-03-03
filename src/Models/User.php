<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Representa un usuario del sistema (tabla `usuarios`).
 */
class User
{
    private int $id;
    private string $username;
    private string $passwordHash;
    private int $roleId;
    private int $estado;

    public function __construct(
        int $id,
        string $username,
        string $passwordHash,
        int $roleId,
        int $estado
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->roleId = $roleId;
        $this->estado = $estado;
    }

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getRoleId(): int { return $this->roleId; }
    public function isActive(): bool { return $this->estado === 1; }
}
