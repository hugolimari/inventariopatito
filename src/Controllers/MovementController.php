<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MovementRepository;

class MovementController extends BaseController
{
    private MovementRepository $moveRepo;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->moveRepo = $container->get('MovementRepository');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->ensureAuthenticated();
    }

    /**
     * Muestra el formulario de salida para un hardware específico
     */
    public function salida(int $id): void
    {
        $current = $this->getCurrentUser();
        // sólo técnicos pueden realizar salidas
        if (($current['role'] ?? 0) !== 3) {
            $this->error('No autorizado', 403);
        }

        if ($this->getMethod() === 'GET') {
            // used only for full‐page view; AJAX clients build their own form
            $this->render('hardware/salida', [
                'data' => ['id' => $id],
                'errors' => []
            ]);
            return;
        }

        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }

        $cantidad = $this->getPost('cantidad');
        $observacion = $this->getPost('observacion');

        $data = ['cantidad' => $cantidad];
        $errors = $this->validate($data, [
            'cantidad' => 'required|integer|min:1'
        ]);

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors], 422);
            } else {
                $this->render('hardware/salida', [
                    'data' => ['id' => $id, 'cantidad' => $cantidad],
                    'errors' => $errors
                ]);
            }
            return;
        }

        try {
            // no tenemos un registro de técnico en esta aplicación, dejar NULL
            $this->moveRepo->registerSalida(
                $current['id'],
                null,
                [['id_hardware' => $id, 'cantidad' => (int)$cantidad]],
                $observacion
            );

            if ($this->isAjax()) {
                // fetch new stock value
                $hardwareRepo = $this->container->get('HardwareRepository');
                $item = $hardwareRepo->findById($id);
                $newStock = $item['stock'] ?? null;
                $this->json(['success' => true, 'message' => 'Salida registrada exitosamente', 'stock' => $newStock]);
            }

            $this->setFlash('success', 'Salida registrada exitosamente');
            $this->redirect('/hardware/index.php');
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            $this->setFlash('error', $e->getMessage());
            $this->redirect("/hardware/salida.php?id={$id}");
        }
    }

    /**
     * Muestra el formulario de entrada (reabastecimiento) para un hardware específico
     */
    public function entrada(int $id): void
    {
        $current = $this->getCurrentUser();
        // solo almacenero (rol 2) y admin (rol 1) pueden registrar entradas
        if (!in_array(($current['role'] ?? 0), [1, 2], true)) {
            $this->error('No autorizado', 403);
        }

        if ($this->getMethod() === 'GET') {
            $hardwareRepo = $this->container->get('HardwareRepository');
            $hardware = $hardwareRepo->findById($id);
            if (!$hardware) {
                $this->error('Hardware no encontrado', 404);
            }
            $this->render('hardware/entrada', [
                'id' => $id,
                'hardware' => $hardware,
                'data' => ['cantidad' => ''],
                'errors' => [],
                'currentUser' => $current
            ]);
            return;
        }

        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }

        $cantidad = $this->getPost('cantidad');
        $observacion = $this->getPost('observacion');

        $data = ['cantidad' => $cantidad];
        $errors = $this->validate($data, [
            'cantidad' => 'required|integer|min:1'
        ]);

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors], 422);
            } else {
                $hardwareRepo = $this->container->get('HardwareRepository');
                $hardware = $hardwareRepo->findById($id);
                $this->render('hardware/entrada', [
                    'id' => $id,
                    'hardware' => $hardware,
                    'data' => ['cantidad' => $cantidad],
                    'errors' => $errors,
                    'currentUser' => $current
                ]);
            }
            return;
        }

        try {
            $this->moveRepo->registerEntrada(
                $current['id'],
                [['id_hardware' => $id, 'cantidad' => (int)$cantidad]],
                $observacion
            );

            if ($this->isAjax()) {
                // fetch new stock value
                $hardwareRepo = $this->container->get('HardwareRepository');
                $item = $hardwareRepo->findById($id);
                $newStock = $item['stock'] ?? null;
                $this->json(['success' => true, 'message' => 'Entrada registrada exitosamente', 'stock' => $newStock]);
            }

            $this->setFlash('success', 'Entrada registrada exitosamente');
            $this->redirect('/hardware/index.php');
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            $this->setFlash('error', $e->getMessage());
            $this->redirect("/hardware/entrada.php?id={$id}");
        }
    }

    /**
     * Muestra el formulario de RMA (devolución)
     */
    public function rma(int $id): void
    {
        $current = $this->getCurrentUser();
        // admin (rol 1), almacenero (rol 2) y técnico (rol 3) pueden registrar RMA
        if (!in_array(($current['role'] ?? 0), [1, 2, 3], true)) {
            $this->error('No autorizado', 403);
        }

        if ($this->getMethod() === 'GET') {
            $hardwareRepo = $this->container->get('HardwareRepository');
            $hardware = $hardwareRepo->findById($id);
            if (!$hardware) {
                $this->error('Hardware no encontrado', 404);
            }
            $this->render('hardware/rma', [
                'id' => $id,
                'hardware' => $hardware,
                'data' => ['cantidad' => '', 'observacion' => ''],
                'errors' => [],
                'currentUser' => $current
            ]);
            return;
        }

        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }

        $cantidad = $this->getPost('cantidad');
        $observacion = $this->getPost('observacion');

        $data = ['cantidad' => $cantidad];
        $errors = $this->validate($data, [
            'cantidad' => 'required|integer|min:1'
        ]);

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors], 422);
                return;
            }

            $hardwareRepo = $this->container->get('HardwareRepository');
            $hardware = $hardwareRepo->findById($id);
            $this->render('hardware/rma', [
                'id' => $id,
                'hardware' => $hardware,
                'data' => ['cantidad' => $cantidad, 'observacion' => $observacion],
                'errors' => $errors,
                'currentUser' => $current
            ]);
            return;
        }

        try {
            $this->moveRepo->registerRMA(
                $current['id'],
                [['id_hardware' => $id, 'cantidad' => (int)$cantidad]],
                $observacion
            );

            if ($this->isAjax()) {
                // fetch new stock value
                $hardwareRepo = $this->container->get('HardwareRepository');
                $item = $hardwareRepo->findById($id);
                $newStock = $item['stock'] ?? null;
                $this->json(['success' => true, 'message' => 'RMA registrado exitosamente', 'stock' => $newStock]);
                return;
            }

            $this->setFlash('success', 'RMA registrado exitosamente');
            $this->redirect('/hardware/index.php');
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 500);
                return;
            }

            $this->setFlash('error', $e->getMessage());
            $this->redirect("/hardware/rma.php?id={$id}");
        }
    }
}
