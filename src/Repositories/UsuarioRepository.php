<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Usuario;
use Database;
use PDO;

/**
 * Repositorio de Usuarios — CRUD con PDO.
 */
class UsuarioRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Busca un usuario por su username.
     */
    public function findByUsername(string $username): ?Usuario
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE username = :username');
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Busca un usuario por su ID.
     */
    public function findById(int $id): ?Usuario
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Obtiene todos los usuarios.
     */
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM usuarios ORDER BY id ASC');
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * Guarda un nuevo usuario con contraseña hasheada (BCRYPT cost 12).
     */
    public function save(string $nombreCompleto, string $username, string $password, string $rol = 'almacenero'): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nombre_completo, username, password_hash, rol)
             VALUES (:nombre, :username, :hash, :rol)'
        );
        $stmt->execute([
            ':nombre'   => $nombreCompleto,
            ':username' => $username,
            ':hash'     => $hash,
            ':rol'      => $rol,
        ]);
    }

    /**
     * Convierte una fila de BD en objeto Usuario.
     */
    private function hydrate(array $row): Usuario
    {
        return new Usuario(
            id: (int) $row['id'],
            nombre_completo: $row['nombre_completo'],
            username: $row['username'],
            password_hash: $row['password_hash'],
            rol: $row['rol']
        );
    }
}
