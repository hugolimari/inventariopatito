<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivoFijoRequest;
use App\Http\Requests\UpdateActivoFijoRequest;
use App\Http\Resources\ActivoFijoResource;
use App\Models\ActivoFijo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $activo = ActivoFijo::create(array_merge(
            $request->validated(),
            ['creado_por' => $request->user()->id]
        ));

        $activo->load('catalogo');

        return response()->json([
            'message' => "Activo fijo S/N {$activo->numero_serie} registrado con éxito.",
            'data'    => new ActivoFijoResource($activo),
        ], 201);
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
        $activo = ActivoFijo::findOrFail($id);
        $activo->update($request->validated());

        return response()->json([
            'message' => 'Activo fijo actualizado con éxito.',
            'data'    => new ActivoFijoResource($activo),
        ]);
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
