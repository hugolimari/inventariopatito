<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Repositorio para gestionar marcas por categoría.
 */
class MarcaRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las marcas.
     * @return array Array de ['id_marca'=>int,'nombre'=>string,'id_categoria'=>int]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id_marca,nombre,id_categoria FROM marcas ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las marcas de una categoría determinada.
     * @return array Array de ['id_marca'=>int,'nombre'=>string]
     */
    public function findByCategory(int $categoryId): array
    {
        $stmt = $this->pdo->prepare("SELECT id_marca,nombre FROM marcas WHERE id_categoria = :cat ORDER BY nombre");
        $stmt->execute(['cat' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una marca por su ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id_marca,nombre,id_categoria FROM marcas WHERE id_marca = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
