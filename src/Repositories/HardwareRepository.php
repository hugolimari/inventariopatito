<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Hardware;
use App\Models\Procesador;
use App\Models\TarjetaGrafica;
use App\Models\MemoriaRAM;
use App\Models\Almacenamiento;
use App\Models\PlacaBase;
use App\Models\FuentePoder;
use Database;
use PDO;

/**
 * Repositorio de Hardware — CRUD con PDO y transacciones.
 * Soporta 6 tipos: Procesador, TarjetaGrafica, MemoriaRAM, Almacenamiento, PlacaBase, FuentePoder.
 */
class HardwareRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ── CREATE ───────────────────────────────────────────────

    public function save(Hardware $item): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO hardware (marca, modelo, precio, stock, categoria, tipo_clase, etiquetado, vida_util_meses, estado, usuario_id)
                 VALUES (:marca, :modelo, :precio, :stock, :categoria, :tipo_clase, :etiquetado, :vida_util, :estado, :usuario_id)'
            );
            $stmt->execute([
                ':marca'      => $item->getMarca(),
                ':modelo'     => $item->getModelo(),
                ':precio'     => $item->getPrecio(),
                ':stock'      => $item->getStock(),
                ':categoria'  => $item->getCategoria(),
                ':tipo_clase' => $item->getTipoClase(),
                ':etiquetado' => $item->getEtiquetado() ? 1 : 0,
                ':vida_util'  => $item->getVidaUtilMeses(),
                ':estado'     => $item->getEstado(),
                ':usuario_id' => $item->getUsuarioId(),
            ]);

            $id = (int) $this->db->lastInsertId();
            $item->setId($id);
            $this->insertarHijo($item, $id);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ── READ ─────────────────────────────────────────────────

    public function findAll(): array
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->db->query($sql . ' ORDER BY h.id DESC');
        return $this->hydrate($stmt->fetchAll());
    }

    public function findById(int $id): ?Hardware
    {
        $sql = $this->buildSelectQuery() . ' WHERE h.id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $items = $this->hydrate([$row]);
        return $items[0] ?? null;
    }

    public function search(string $query): array
    {
        $sql = $this->buildSelectQuery() . ' WHERE h.marca LIKE :q OR h.modelo LIKE :q2 OR h.categoria LIKE :q3 ORDER BY h.id DESC';
        $term = '%' . $query . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':q' => $term, ':q2' => $term, ':q3' => $term]);
        return $this->hydrate($stmt->fetchAll());
    }

    // ── UPDATE ───────────────────────────────────────────────

    public function update(Hardware $item): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'UPDATE hardware SET marca = :marca, modelo = :modelo, precio = :precio,
                        stock = :stock, categoria = :categoria, tipo_clase = :tipo_clase, etiquetado = :etiquetado,
                        vida_util_meses = :vida_util, estado = :estado, usuario_id = :usuario_id
                 WHERE id = :id'
            );
            $stmt->execute([
                ':marca'      => $item->getMarca(),
                ':modelo'     => $item->getModelo(),
                ':precio'     => $item->getPrecio(),
                ':stock'      => $item->getStock(),
                ':categoria'  => $item->getCategoria(),
                ':tipo_clase' => $item->getTipoClase(),
                ':etiquetado' => $item->getEtiquetado() ? 1 : 0,
                ':vida_util'  => $item->getVidaUtilMeses(),
                ':estado'     => $item->getEstado(),
                ':usuario_id' => $item->getUsuarioId(),
                ':id'         => $item->getId(),
            ]);

            $this->actualizarHijo($item);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ── DELETE ───────────────────────────────────────────────

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM hardware WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    // ══════════════════════════════════════════════════════════
    //  HELPERS PRIVADOS
    // ══════════════════════════════════════════════════════════

    private function buildSelectQuery(): string
    {
        return 'SELECT h.*,
                    p.nucleos, p.frecuencia,
                    tg.vram,
                    mr.capacidad AS ram_capacidad, mr.tipo AS ram_tipo, mr.velocidad AS ram_velocidad,
                    al.capacidad AS alm_capacidad, al.tipo AS alm_tipo, al.velocidad_lectura AS alm_velocidad,
                    pb.socket, pb.formato,
                    fp.potencia, fp.certificacion
                FROM hardware h
                LEFT JOIN procesadores p ON h.id = p.hardware_id
                LEFT JOIN tarjetas_graficas tg ON h.id = tg.hardware_id
                LEFT JOIN memorias_ram mr ON h.id = mr.hardware_id
                LEFT JOIN almacenamiento al ON h.id = al.hardware_id
                LEFT JOIN placas_base pb ON h.id = pb.hardware_id
                LEFT JOIN fuentes_poder fp ON h.id = fp.hardware_id';
    }

    private function insertarHijo(Hardware $item, int $id): void
    {
        if ($item instanceof Procesador) {
            $stmt = $this->db->prepare('INSERT INTO procesadores (hardware_id, nucleos, frecuencia) VALUES (:id, :nucleos, :frecuencia)');
            $stmt->execute([':id' => $id, ':nucleos' => $item->getNucleos(), ':frecuencia' => $item->getFrecuencia()]);
        } elseif ($item instanceof TarjetaGrafica) {
            $stmt = $this->db->prepare('INSERT INTO tarjetas_graficas (hardware_id, vram) VALUES (:id, :vram)');
            $stmt->execute([':id' => $id, ':vram' => $item->getVram()]);
        } elseif ($item instanceof MemoriaRAM) {
            $stmt = $this->db->prepare('INSERT INTO memorias_ram (hardware_id, capacidad, tipo, velocidad) VALUES (:id, :cap, :tipo, :vel)');
            $stmt->execute([':id' => $id, ':cap' => $item->getCapacidad(), ':tipo' => $item->getTipo(), ':vel' => $item->getVelocidad()]);
        } elseif ($item instanceof Almacenamiento) {
            $stmt = $this->db->prepare('INSERT INTO almacenamiento (hardware_id, capacidad, tipo, velocidad_lectura) VALUES (:id, :cap, :tipo, :vel)');
            $stmt->execute([':id' => $id, ':cap' => $item->getCapacidad(), ':tipo' => $item->getTipoAlmacenamiento(), ':vel' => $item->getVelocidadLectura()]);
        } elseif ($item instanceof PlacaBase) {
            $stmt = $this->db->prepare('INSERT INTO placas_base (hardware_id, socket, formato) VALUES (:id, :socket, :formato)');
            $stmt->execute([':id' => $id, ':socket' => $item->getSocket(), ':formato' => $item->getFormato()]);
        } elseif ($item instanceof FuentePoder) {
            $stmt = $this->db->prepare('INSERT INTO fuentes_poder (hardware_id, potencia, certificacion) VALUES (:id, :pot, :cert)');
            $stmt->execute([':id' => $id, ':pot' => $item->getPotencia(), ':cert' => $item->getCertificacion()]);
        }
    }

    private function actualizarHijo(Hardware $item): void
    {
        $id = $item->getId();
        if ($item instanceof Procesador) {
            $stmt = $this->db->prepare('UPDATE procesadores SET nucleos = :nucleos, frecuencia = :freq WHERE hardware_id = :id');
            $stmt->execute([':nucleos' => $item->getNucleos(), ':freq' => $item->getFrecuencia(), ':id' => $id]);
        } elseif ($item instanceof TarjetaGrafica) {
            $stmt = $this->db->prepare('UPDATE tarjetas_graficas SET vram = :vram WHERE hardware_id = :id');
            $stmt->execute([':vram' => $item->getVram(), ':id' => $id]);
        } elseif ($item instanceof MemoriaRAM) {
            $stmt = $this->db->prepare('UPDATE memorias_ram SET capacidad = :cap, tipo = :tipo, velocidad = :vel WHERE hardware_id = :id');
            $stmt->execute([':cap' => $item->getCapacidad(), ':tipo' => $item->getTipo(), ':vel' => $item->getVelocidad(), ':id' => $id]);
        } elseif ($item instanceof Almacenamiento) {
            $stmt = $this->db->prepare('UPDATE almacenamiento SET capacidad = :cap, tipo = :tipo, velocidad_lectura = :vel WHERE hardware_id = :id');
            $stmt->execute([':cap' => $item->getCapacidad(), ':tipo' => $item->getTipoAlmacenamiento(), ':vel' => $item->getVelocidadLectura(), ':id' => $id]);
        } elseif ($item instanceof PlacaBase) {
            $stmt = $this->db->prepare('UPDATE placas_base SET socket = :socket, formato = :fmt WHERE hardware_id = :id');
            $stmt->execute([':socket' => $item->getSocket(), ':fmt' => $item->getFormato(), ':id' => $id]);
        } elseif ($item instanceof FuentePoder) {
            $stmt = $this->db->prepare('UPDATE fuentes_poder SET potencia = :pot, certificacion = :cert WHERE hardware_id = :id');
            $stmt->execute([':pot' => $item->getPotencia(), ':cert' => $item->getCertificacion(), ':id' => $id]);
        }
    }

    /**
     * Convierte filas en objetos según tipo_clase.
     */
    private function hydrate(array $rows): array
    {
        $items = [];
        foreach ($rows as $r) {
            $base = [
                'id' => (int)$r['id'], 'marca' => $r['marca'], 'modelo' => $r['modelo'],
                'precio' => (float)$r['precio'], 'stock' => (int)$r['stock'], 'categoria' => $r['categoria'],
                'etiquetado' => (bool)$r['etiquetado'], 'vida_util_meses' => (int)$r['vida_util_meses'],
                'estado' => $r['estado'], 'usuario_id' => (int)$r['usuario_id'],
            ];

            $item = match($r['tipo_clase']) {
                'Procesador' => new Procesador(
                    $base['id'], $base['marca'], $base['modelo'], $base['precio'], $base['stock'], $base['categoria'],
                    (int)$r['nucleos'], $r['frecuencia'],
                    $base['etiquetado'], $base['vida_util_meses'], $base['estado'], $base['usuario_id']
                ),
                'TarjetaGrafica' => new TarjetaGrafica(
                    $base['id'], $base['marca'], $base['modelo'], $base['precio'], $base['stock'], $base['categoria'],
                    $r['vram'],
                    $base['etiquetado'], $base['vida_util_meses'], $base['estado'], $base['usuario_id']
                ),
                'MemoriaRAM' => new MemoriaRAM(
                    $base['id'], $base['marca'], $base['modelo'], $base['precio'], $base['stock'], $base['categoria'],
                    $r['ram_capacidad'], $r['ram_tipo'], $r['ram_velocidad'],
                    $base['etiquetado'], $base['vida_util_meses'], $base['estado'], $base['usuario_id']
                ),
                'Almacenamiento' => new Almacenamiento(
                    $base['id'], $base['marca'], $base['modelo'], $base['precio'], $base['stock'], $base['categoria'],
                    $r['alm_capacidad'], $r['alm_tipo'], $r['alm_velocidad'],
                    $base['etiquetado'], $base['vida_util_meses'], $base['estado'], $base['usuario_id']
                ),
                'PlacaBase' => new PlacaBase(
                    $base['id'], $base['marca'], $base['modelo'], $base['precio'], $base['stock'], $base['categoria'],
                    $r['socket'], $r['formato'],
                    $base['etiquetado'], $base['vida_util_meses'], $base['estado'], $base['usuario_id']
                ),
                'FuentePoder' => new FuentePoder(
                    $base['id'], $base['marca'], $base['modelo'], $base['precio'], $base['stock'], $base['categoria'],
                    $r['potencia'], $r['certificacion'],
                    $base['etiquetado'], $base['vida_util_meses'], $base['estado'], $base['usuario_id']
                ),
                default => null,
            };

            if ($item !== null) {
                $items[] = $item;
            }
        }
        return $items;
    }
}
