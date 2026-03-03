<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use PDO;

/**
 * Repositorio encargado de las operaciones de autenticación.
 * Internamente utiliza PDO para conectarse a la base de datos.
 */
class AuthRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca un usuario por su nombre de usuario.
     *
     * @return User|null
     */
    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM usuarios WHERE username = :u LIMIT 1'
        );
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new User(
            (int) $row['id_usuario'],
            $row['username'],
            $row['password_hash'],
            (int) $row['id_rol'],
            (int) $row['estado']
        );
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     * Retorna el ID generado.
     * Lance una excepción si el nombre de usuario ya existe.
     */
    public function createUser(string $username, string $passwordHash, int $roleId): int
    {
        // verificar duplicado
        if ($this->findByUsername($username) !== null) {
            throw new \Exception('Nombre de usuario ya existe');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (username, password_hash, id_rol, estado) VALUES (:u, :p, :r, 1)'
        );
        $stmt->execute([
            'u' => $username,
            'p' => $passwordHash,
            'r' => $roleId
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Obtiene todos los usuarios con sus roles.
     * @return array Cada elemento: [id_usuario, username, id_rol, nombre (rol), estado]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT u.id_usuario, u.username, u.id_rol, r.nombre as rol_nombre, u.estado, u.created_at 
             FROM usuarios u 
             LEFT JOIN roles r ON u.id_rol = r.id_rol 
             ORDER BY u.id_usuario DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por su ID.
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id_usuario, u.username, u.id_rol, r.nombre as rol_nombre, u.estado, u.created_at 
             FROM usuarios u 
             LEFT JOIN roles r ON u.id_rol = r.id_rol 
             WHERE u.id_usuario = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Actualiza datos de un usuario (rol, estado, contraseña).
     * @param int $id id del usuario
     * @param array $data cambios (id_rol, estado, password_hash)
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
    {
        $setClauses = [];
        $params = ['id' => $id];

        $allowedFields = ['id_rol', 'estado', 'password_hash'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $setClauses[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($setClauses)) {
            return true; // nada que actualizar
        }

        $sql = 'UPDATE usuarios SET ' . implode(', ', $setClauses) . ' WHERE id_usuario = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Elimina un usuario (borrado duro).
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id_usuario = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Alterna el estado de un usuario (activo/inactivo).
     * @param int $id
     * @return bool
     */
    public function toggleUserState(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios SET estado = IF(estado = 1, 0, 1) WHERE id_usuario = :id'
        );
        return $stmt->execute(['id' => $id]);
    }
}
