<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Repositorio para gestionar categorías desde la tabla `categorias`.
 */
class CategoryRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las categorías.
     * @return array Array de ['id_categoria' => int, 'nombre' => string]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una categoría por ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id_categoria, nombre FROM categorias WHERE id_categoria = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
