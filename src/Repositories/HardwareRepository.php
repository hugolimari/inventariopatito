<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Repositorio para gestionar hardware desde la tabla `hardware`.
 * Usa PDO para persistencia en la base de datos.
 */
class HardwareRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los items de hardware con su información de categoría.
     */
    public function findAll(): array
    {
        // include brand name from marcas table
        $stmt = $this->pdo->query(
            "SELECT h.*, c.nombre as categoria_nombre, m.nombre as marca_nombre 
             FROM hardware h 
             JOIN categorias c ON h.id_categoria = c.id_categoria 
             LEFT JOIN marcas m ON h.id_marca = m.id_marca 
             WHERE h.estado = 1 
             ORDER BY h.id_hardware DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un item por su ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT h.*, c.nombre as categoria_nombre, m.nombre as marca_nombre 
             FROM hardware h 
             JOIN categorias c ON h.id_categoria = c.id_categoria 
             LEFT JOIN marcas m ON h.id_marca = m.id_marca 
             WHERE h.id_hardware = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Guarda un nuevo item de hardware en la BD.
     */
    public function save(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO hardware (id_categoria, id_marca, modelo, precio, stock, estado) 
             VALUES (:id_categoria, :id_marca, :modelo, :precio, :stock, 1)"
        );
        $stmt->execute([
            'id_categoria' => (int) $data['id_categoria'],
            'id_marca' => (int) $data['id_marca'],
            'modelo' => (string) $data['modelo'],
            'precio' => (float) $data['precio'],
            'stock' => (int) $data['stock']
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Actualiza un item de hardware existente (solo los campos provided).
     */
    public function update(int $id, array $data): bool
    {
        // Build the SET clause dynamically based on provided fields
        $setClauses = [];
        $params = ['id' => $id];
        
        $allowedFields = ['id_categoria', 'id_marca', 'modelo', 'precio', 'stock'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $setClauses[] = "$field = :$field";
                // cast integers for numeric columns
                if (in_array($field, ['id_categoria','id_marca','stock'], true)) {
                    $params[$field] = (int) $data[$field];
                } elseif ($field === 'precio') {
                    $params[$field] = (float) $data[$field];
                } else {
                    $params[$field] = $data[$field];
                }
            }
        }
        
        if (empty($setClauses)) {
            return true; // No fields to update
        }
        
        $sql = "UPDATE hardware SET " . implode(', ', $setClauses) . " WHERE id_hardware = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Marca un item como eliminado (soft delete).
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE hardware SET estado = 0 WHERE id_hardware = :id");
        return $stmt->execute(['id' => $id]);
    }
}
