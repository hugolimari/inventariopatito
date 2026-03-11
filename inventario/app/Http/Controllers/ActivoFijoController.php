<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivoFijoRequest;
use App\Http\Requests\UpdateActivoFijoRequest;
use App\Http\Resources\ActivoFijoResource;
use App\Models\ActivoFijo;
use App\Models\KardexMovimiento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivoFijoController extends Controller
{
    /**
     * Lista todos los activos fijos con sus relaciones.
     */
    public function index(): JsonResponse
    {
        $activos = ActivoFijo::with(['catalogo', 'asignadoA', 'creadoPor'])->get();

        return response()->json([
            'data' => ActivoFijoResource::collection($activos),
        ]);
    }

    /**
     * Registra un nuevo activo fijo (serializado).
     * El campo creado_por se asigna automáticamente al usuario autenticado.
     */
    public function store(StoreActivoFijoRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $activo = ActivoFijo::create(array_merge(
                $request->validated(),
                ['creado_por' => $request->user()->id]
            ));

            // Registro automático en el Kardex al ingresar
            KardexMovimiento::create([
                'tipo_movimiento'   => 'Ingreso',
                'activo_fijo_id'    => $activo->id,
                'operador_id'       => $request->user()->id,
                'cantidad_afectada' => 1,
                'observaciones'     => 'Ingreso inicial al sistema',
            ]);

            DB::commit();

            $activo->load('catalogo');

            return response()->json([
                'message' => "Activo fijo S/N {$activo->numero_serie} registrado con éxito.",
                'data'    => new ActivoFijoResource($activo),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al registrar activo.', 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * Muestra un activo fijo específico.
     */
    public function show(string $id): JsonResponse
    {
        $activo = ActivoFijo::with(['catalogo', 'asignadoA', 'creadoPor'])->findOrFail($id);

        return response()->json([
            'data' => new ActivoFijoResource($activo),
        ]);
    }

    /**
     * Actualiza un activo fijo.
     */
    public function update(UpdateActivoFijoRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $activo = ActivoFijo::findOrFail($id);
            $datosViejosAsignado = $activo->asignado_a;
            $datosViejosEstado = $activo->estado;
            
            $activo->update($request->validated());

            // Detectar si fue asignado a alguien (Check-out) o devuelto (Check-in)
            if ($activo->asignado_a !== $datosViejosAsignado) {
                if ($activo->asignado_a !== null && $datosViejosAsignado === null) {
                    KardexMovimiento::create([
                        'tipo_movimiento'   => 'Check-out',
                        'activo_fijo_id'    => $activo->id,
                        'operador_id'       => $request->user()->id,
                        'receptor_id'       => $activo->asignado_a,
                        'cantidad_afectada' => 1,
                        'observaciones'     => 'Asignación desde edición de activo',
                    ]);
                } elseif ($activo->asignado_a === null && $datosViejosAsignado !== null) {
                    KardexMovimiento::create([
                        'tipo_movimiento'   => 'Check-in',
                        'activo_fijo_id'    => $activo->id,
                        'operador_id'       => $request->user()->id,
                        'cantidad_afectada' => 1,
                        'observaciones'     => 'Devolución desde edición de activo',
                    ]);
                }
            }
            
            // Detectar si cambian a Dado de Baja manually
            if ($activo->estado === 'Dado de Baja' && $datosViejosEstado !== 'Dado de Baja') {
                 KardexMovimiento::create([
                        'tipo_movimiento'   => 'Baja',
                        'activo_fijo_id'    => $activo->id,
                        'operador_id'       => $request->user()->id,
                        'cantidad_afectada' => 1,
                        'observaciones'     => 'Dado de baja manual',
                 ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Activo fijo actualizado con éxito.',
                'data'    => new ActivoFijoResource($activo),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar activo.', 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * SoftDelete de un activo fijo.
     */
    public function destroy(string $id): JsonResponse
    {
        $activo = ActivoFijo::findOrFail($id);
        $activo->delete();

        return response()->json([
            'message' => "Activo fijo S/N {$activo->numero_serie} dado de baja.",
        ]);
    }
}
