<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Hardware;
use App\Models\Procesador;
use App\Models\TarjetaGrafica;
use Database;
use PDO;

/**
 * Repositorio de Hardware — CRUD con PDO y transacciones.
 * Implementa Class Table Inheritance: hardware + procesadores/tarjetas_graficas.
 */
class HardwareRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ── CREATE ───────────────────────────────────────────────

    /**
     * Guarda un nuevo hardware con transacción:
     * 1. INSERT en `hardware` → obtener lastInsertId()
     * 2. INSERT en tabla hija según el tipo de objeto.
     */
    public function save(Hardware $item): void
    {
        $this->db->beginTransaction();

        try {
            // 1. Insertar en tabla base `hardware`
            $stmt = $this->db->prepare(
                'INSERT INTO hardware (marca, modelo, precio, stock, categoria, etiquetado, vida_util_meses, estado, usuario_id)
                 VALUES (:marca, :modelo, :precio, :stock, :categoria, :etiquetado, :vida_util, :estado, :usuario_id)'
            );
            $stmt->execute([
                ':marca'      => $item->getMarca(),
                ':modelo'     => $item->getModelo(),
                ':precio'     => $item->getPrecio(),
                ':stock'      => $item->getStock(),
                ':categoria'  => $item->getCategoria(),
                ':etiquetado' => $item->getEtiquetado() ? 1 : 0,
                ':vida_util'  => $item->getVidaUtilMeses(),
                ':estado'     => $item->getEstado(),
                ':usuario_id' => $item->getUsuarioId(),
            ]);

            $hardwareId = (int) $this->db->lastInsertId();
            $item->setId($hardwareId);

            // 2. Insertar en tabla hija
            if ($item instanceof Procesador) {
                $stmt = $this->db->prepare(
                    'INSERT INTO procesadores (hardware_id, nucleos, frecuencia)
                     VALUES (:id, :nucleos, :frecuencia)'
                );
                $stmt->execute([
                    ':id'         => $hardwareId,
                    ':nucleos'    => $item->getNucleos(),
                    ':frecuencia' => $item->getFrecuencia(),
                ]);
            } elseif ($item instanceof TarjetaGrafica) {
                $stmt = $this->db->prepare(
                    'INSERT INTO tarjetas_graficas (hardware_id, vram)
                     VALUES (:id, :vram)'
                );
                $stmt->execute([
                    ':id'   => $hardwareId,
                    ':vram' => $item->getVram(),
                ]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ── READ ─────────────────────────────────────────────────

    /**
     * Obtiene todos los items con LEFT JOIN a tablas hijas.
     */
    public function findAll(): array
    {
        $sql = 'SELECT h.*, p.nucleos, p.frecuencia, tg.vram
                FROM hardware h
                LEFT JOIN procesadores p ON h.id = p.hardware_id
                LEFT JOIN tarjetas_graficas tg ON h.id = tg.hardware_id
                ORDER BY h.id DESC';

        $stmt = $this->db->query($sql);
        return $this->hydrate($stmt->fetchAll());
    }

    /**
     * Busca un hardware por su ID.
     */
    public function findById(int $id): ?Hardware
    {
        $sql = 'SELECT h.*, p.nucleos, p.frecuencia, tg.vram
                FROM hardware h
                LEFT JOIN procesadores p ON h.id = p.hardware_id
                LEFT JOIN tarjetas_graficas tg ON h.id = tg.hardware_id
                WHERE h.id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $items = $this->hydrate([$row]);
        return $items[0];
    }

    /**
     * Búsqueda por texto (LIKE) en marca, modelo o categoría.
     * Previene SQL Injection con Prepared Statements.
     */
    public function search(string $query): array
    {
        $sql = 'SELECT h.*, p.nucleos, p.frecuencia, tg.vram
                FROM hardware h
                LEFT JOIN procesadores p ON h.id = p.hardware_id
                LEFT JOIN tarjetas_graficas tg ON h.id = tg.hardware_id
                WHERE h.marca LIKE :q OR h.modelo LIKE :q2 OR h.categoria LIKE :q3
                ORDER BY h.id DESC';

        $term = '%' . $query . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':q' => $term, ':q2' => $term, ':q3' => $term]);

        return $this->hydrate($stmt->fetchAll());
    }

    // ── UPDATE ───────────────────────────────────────────────

    /**
     * Actualiza un hardware existente con transacción en ambas tablas.
     */
    public function update(Hardware $item): void
    {
        $this->db->beginTransaction();

        try {
            // 1. Actualizar tabla base
            $stmt = $this->db->prepare(
                'UPDATE hardware SET marca = :marca, modelo = :modelo, precio = :precio,
                        stock = :stock, categoria = :categoria, etiquetado = :etiquetado,
                        vida_util_meses = :vida_util, estado = :estado, usuario_id = :usuario_id
                 WHERE id = :id'
            );
            $stmt->execute([
                ':marca'      => $item->getMarca(),
                ':modelo'     => $item->getModelo(),
                ':precio'     => $item->getPrecio(),
                ':stock'      => $item->getStock(),
                ':categoria'  => $item->getCategoria(),
                ':etiquetado' => $item->getEtiquetado() ? 1 : 0,
                ':vida_util'  => $item->getVidaUtilMeses(),
                ':estado'     => $item->getEstado(),
                ':usuario_id' => $item->getUsuarioId(),
                ':id'         => $item->getId(),
            ]);

            // 2. Actualizar tabla hija
            if ($item instanceof Procesador) {
                $stmt = $this->db->prepare(
                    'UPDATE procesadores SET nucleos = :nucleos, frecuencia = :frecuencia
                     WHERE hardware_id = :id'
                );
                $stmt->execute([
                    ':nucleos'    => $item->getNucleos(),
                    ':frecuencia' => $item->getFrecuencia(),
                    ':id'         => $item->getId(),
                ]);
            } elseif ($item instanceof TarjetaGrafica) {
                $stmt = $this->db->prepare(
                    'UPDATE tarjetas_graficas SET vram = :vram
                     WHERE hardware_id = :id'
                );
                $stmt->execute([
                    ':vram' => $item->getVram(),
                    ':id'   => $item->getId(),
                ]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ── DELETE ───────────────────────────────────────────────

    /**
     * Elimina un hardware por ID (CASCADE borra la tabla hija automáticamente).
     */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM hardware WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    // ── HELPERS ──────────────────────────────────────────────

    /**
     * Convierte filas de la BD en objetos Procesador o TarjetaGrafica.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return Hardware[]
     */
    private function hydrate(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            if ($row['nucleos'] !== null) {
                // Es un Procesador
                $items[] = new Procesador(
                    id: (int) $row['id'],
                    marca: $row['marca'],
                    modelo: $row['modelo'],
                    precio: (float) $row['precio'],
                    stock: (int) $row['stock'],
                    categoria: $row['categoria'],
                    nucleos: (int) $row['nucleos'],
                    frecuencia: $row['frecuencia'],
                    etiquetado: (bool) $row['etiquetado'],
                    vida_util_meses: (int) $row['vida_util_meses'],
                    estado: $row['estado'],
                    usuario_id: (int) $row['usuario_id']
                );
            } elseif ($row['vram'] !== null) {
                // Es una Tarjeta Gráfica
                $items[] = new TarjetaGrafica(
                    id: (int) $row['id'],
                    marca: $row['marca'],
                    modelo: $row['modelo'],
                    precio: (float) $row['precio'],
                    stock: (int) $row['stock'],
                    categoria: $row['categoria'],
                    vram: $row['vram'],
                    etiquetado: (bool) $row['etiquetado'],
                    vida_util_meses: (int) $row['vida_util_meses'],
                    estado: $row['estado'],
                    usuario_id: (int) $row['usuario_id']
                );
            }
        }

        return $items;
    }
}
