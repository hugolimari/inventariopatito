<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKardexMovimientoRequest;
use App\Http\Resources\KardexMovimientoResource;
use App\Models\ActivoFijo;
use App\Models\KardexMovimiento;
use App\Models\LoteConsumible;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class KardexMovimientoController extends Controller
{
    /**
     * Lista todos los movimientos del Kardex.
     */
    public function index(): JsonResponse
    {
        $movimientos = KardexMovimiento::with([
            'operador', 'receptor', 'activoFijo.catalogo', 'loteConsumible.catalogo',
        ])->orderByDesc('created_at')->get();

        return response()->json([
            'data' => KardexMovimientoResource::collection($movimientos),
        ]);
    }

    /**
     * Historias #06, #07, #09, #13 – Registrar movimiento con DB Transaction.
     *
     * Tipos soportados:
     *  - Ingreso   → Suma stock al lote consumible (Historia #06)
     *  - Check-out → Cambia estado del activo a 'Asignado' o descuenta del lote (Historia #07 y #13)
     *  - Check-in  → Devuelve activo a 'En Almacén' o suma al lote
     *  - RMA       → Marca activo como 'Defectuoso' o descuenta del lote (Historia #09)
     *  - Baja      → Marca activo como 'Dado de Baja'
     *  - Venta     → Marca activo como 'Vendido'
     */
    public function store(StoreKardexMovimientoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tipo = $data['tipo_movimiento'];

        try {
            DB::beginTransaction();

            // ── Operaciones sobre ACTIVO FIJO (Serializado) ──────────
            if (! empty($data['activo_fijo_id'])) {
                $activo = ActivoFijo::findOrFail($data['activo_fijo_id']);

                match ($tipo) {
                    'Check-out' => $this->checkoutActivo($activo, $data),
                    'Check-in'  => $activo->update(['estado' => 'En Almacén', 'asignado_a' => null]),
                    'RMA'       => $activo->update(['estado' => 'Defectuoso']),
                    'Baja'      => $activo->update(['estado' => 'Dado de Baja']),
                    'Venta'     => $activo->update(['estado' => 'Vendido']),
                    'Ingreso'   => $activo->update(['estado' => 'En Almacén']),
                };
            }

            // ── Operaciones sobre LOTE CONSUMIBLE ────────────────────
            if (! empty($data['lote_consumible_id'])) {
                $lote = LoteConsumible::findOrFail($data['lote_consumible_id']);
                $cantidad = $data['cantidad_afectada'];

                match ($tipo) {
                    'Ingreso'  => $lote->increment('cantidad_disponible', $cantidad),
                    'Check-out', 'RMA', 'Baja', 'Venta' => $this->descontarLote($lote, $cantidad),
                    'Check-in' => $lote->increment('cantidad_disponible', $cantidad),
                };
            }

            // ── Crear el registro en el Kardex ───────────────────────
            $movimiento = KardexMovimiento::create(array_merge(
                $data,
                ['operador_id' => $request->user()->id]
            ));

            DB::commit();

            $movimiento->load(['operador', 'receptor', 'activoFijo.catalogo', 'loteConsumible.catalogo']);

            return response()->json([
                'message' => "Movimiento de tipo '{$tipo}' registrado con éxito.",
                'data'    => new KardexMovimientoResource($movimiento),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al registrar el movimiento.',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Muestra un movimiento específico.
     */
    public function show(string $id): JsonResponse
    {
        $movimiento = KardexMovimiento::with([
            'operador', 'receptor', 'activoFijo.catalogo', 'loteConsumible.catalogo',
        ])->findOrFail($id);

        return response()->json([
            'data' => new KardexMovimientoResource($movimiento),
        ]);
    }

    // ─── Métodos privados ──────────────────────────────────────

    /**
     * Historia #07 – Check-out de un activo fijo.
     * Valida que esté en almacén y cambia estado a 'Asignado'.
     */
    private function checkoutActivo(ActivoFijo $activo, array $data): void
    {
        if ($activo->estado !== 'En Almacén') {
            throw new \Exception("El activo S/N {$activo->numero_serie} no está disponible (estado actual: {$activo->estado}).");
        }

        $activo->update([
            'estado'     => 'Asignado',
            'asignado_a' => $data['receptor_id'] ?? null,
        ]);
    }

    /**
     * Historia #07 – Descuento de stock de un lote consumible.
     * Valida que haya stock suficiente antes de descontar.
     */
    private function descontarLote(LoteConsumible $lote, int $cantidad): void
    {
        if ($lote->cantidad_disponible < $cantidad) {
            throw new \Exception(
                "Stock insuficiente. Disponible: {$lote->cantidad_disponible}, solicitado: {$cantidad}."
            );
        }

        $lote->decrement('cantidad_disponible', $cantidad);
    }
}
