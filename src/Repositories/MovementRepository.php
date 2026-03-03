<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class MovementRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function registerSalida(int $userId, ?int $technicianId, array $items, ?string $observacion = null): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO movimientos (tipo, id_usuario, observacion) 
                 VALUES ('SALIDA', :u, :o)"
            );
            $stmt->execute(['u' => $userId, 'o' => $observacion]);
            $movId = (int) $this->pdo->lastInsertId();

            $updateStmt = $this->pdo->prepare(
                "UPDATE hardware SET stock = stock - :cnt 
                 WHERE id_hardware = :id AND stock >= :cnt"
            );
            $detailStmt = $this->pdo->prepare(
                "INSERT INTO detalle_movimiento (id_movimiento, id_hardware, cantidad) 
                 VALUES (:m, :h, :c)"
            );

            foreach ($items as $item) {
                $cnt = (int) $item['cantidad'];
                $hid = (int) $item['id_hardware'];

                if ($cnt <= 0) {
                    throw new \Exception("Cantidad inválida para hardware ID $hid");
                }

                $updateStmt->execute(['cnt' => $cnt, 'id' => $hid]);
                if ($updateStmt->rowCount() === 0) {
                    throw new \Exception("Stock insuficiente para hardware ID $hid");
                }

                $detailStmt->execute(['m' => $movId, 'h' => $hid, 'c' => $cnt]);
            }

            $this->pdo->commit();
            return $movId;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Registra una entrada de componentes (compra o reabastecimiento).
     *
     * @param int $userId id del usuario que realiza la operación (almacenero)
     * @param array $items arreglo de elementos con keys id_hardware y cantidad
     * @param string|null $observacion texto opcional (ej: número de factura, proveedor)
     * @return int id del movimiento creado
     * @throws \Exception si falla la transacción
     */
    public function registerEntrada(int $userId, array $items, ?string $observacion = null): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO movimientos (id_usuario, tipo, observacion) 
                 VALUES (:u, 'ENTRADA', :o)"
            );
            $stmt->execute(['u' => $userId, 'o' => $observacion]);

            $movId = (int) $this->pdo->lastInsertId();

            $updateStmt = $this->pdo->prepare(
                "UPDATE hardware SET stock = stock + :cnt 
                 WHERE id_hardware = :id"
            );
            $detailStmt = $this->pdo->prepare(
                "INSERT INTO detalle_movimiento (id_movimiento, id_hardware, cantidad) 
                 VALUES (:m, :h, :c)"
            );

            foreach ($items as $item) {
                $cnt = (int) $item['cantidad'];
                $hid = (int) $item['id_hardware'];

                if ($cnt <= 0) {
                    throw new \Exception("Cantidad inválida para hardware ID $hid");
                }

                $updateStmt->execute(['cnt' => $cnt, 'id' => $hid]);
                $detailStmt->execute(['m' => $movId, 'h' => $hid, 'c' => $cnt]);
            }

            $this->pdo->commit();
            return $movId;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Registra un RMA (devolución/reclamo de componentes).
     *
     * @param int $userId id del usuario que realiza el RMA
     * @param array $items arreglo de elementos con keys id_hardware y cantidad
     * @param string|null $observacion texto con motivo del RMA
     * @return int id del movimiento creado
     * @throws \Exception si falla la transacción
     */
    public function registerRMA(int $userId, array $items, ?string $observacion = null): int
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO movimientos (id_usuario, tipo, observacion) 
                 VALUES (:u, 'RMA', :o)"
            );
            $stmt->execute(['u' => $userId, 'o' => $observacion]);

            $movId = (int) $this->pdo->lastInsertId();

            $updateStmt = $this->pdo->prepare(
                "UPDATE hardware SET stock = stock + :cnt 
                 WHERE id_hardware = :id"
            );
            $detailStmt = $this->pdo->prepare(
                "INSERT INTO detalle_movimiento (id_movimiento, id_hardware, cantidad) 
                 VALUES (:m, :h, :c)"
            );

            foreach ($items as $item) {
                $cnt = (int) $item['cantidad'];
                $hid = (int) $item['id_hardware'];

                if ($cnt <= 0) {
                    throw new \Exception("Cantidad inválida para hardware ID $hid");
                }

                $updateStmt->execute(['cnt' => $cnt, 'id' => $hid]);
                $detailStmt->execute(['m' => $movId, 'h' => $hid, 'c' => $cnt]);
            }

            $this->pdo->commit();
            return $movId;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
