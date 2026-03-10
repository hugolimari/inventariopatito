<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoteConsumibleRequest;
use App\Http\Requests\UpdateLoteConsumibleRequest;
use App\Http\Resources\LoteConsumibleResource;
use App\Models\LoteConsumible;
use Illuminate\Http\JsonResponse;

class LoteConsumibleController extends Controller
{
    /**
     * Lista todos los lotes consumibles.
     */
    public function index(): JsonResponse
    {
        $lotes = LoteConsumible::with(['catalogo', 'creadoPor'])->get();

        return response()->json([
            'data' => LoteConsumibleResource::collection($lotes),
        ]);
    }

    /**
     * Registra un nuevo lote consumible.
     */
    public function store(StoreLoteConsumibleRequest $request): JsonResponse
    {
        $lote = LoteConsumible::create(array_merge(
            $request->validated(),
            ['creado_por' => $request->user()->id]
        ));

        $lote->load('catalogo');

        return response()->json([
            'message' => 'Lote consumible registrado con éxito.',
            'data'    => new LoteConsumibleResource($lote),
        ], 201);
    }

    /**
     * Muestra un lote consumible específico.
     */
    public function show(string $id): JsonResponse
    {
        $lote = LoteConsumible::with(['catalogo', 'creadoPor'])->findOrFail($id);

        return response()->json([
            'data' => new LoteConsumibleResource($lote),
        ]);
    }

    /**
     * Actualiza un lote consumible.
     */
    public function update(UpdateLoteConsumibleRequest $request, string $id): JsonResponse
    {
        $lote = LoteConsumible::findOrFail($id);
        $lote->update($request->validated());

        return response()->json([
            'message' => 'Lote consumible actualizado con éxito.',
            'data'    => new LoteConsumibleResource($lote),
        ]);
    }

    /**
     * SoftDelete de un lote consumible.
     */
    public function destroy(string $id): JsonResponse
    {
        $lote = LoteConsumible::findOrFail($id);
        $lote->delete();

        return response()->json([
            'message' => 'Lote consumible dado de baja con éxito.',
        ]);
    }
}
