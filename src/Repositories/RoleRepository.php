<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Repositorio para manejar los roles de usuario.
 */
class RoleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Devuelve todos los roles disponibles.
     * @return array Cada elemento es ['id_rol' => int, 'nombre' => string]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id_rol, nombre FROM roles ORDER BY id_rol');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un rol por id.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id_rol, nombre FROM roles WHERE id_rol = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
