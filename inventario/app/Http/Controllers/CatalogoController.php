<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogoRequest;
use App\Http\Requests\UpdateCatalogoRequest;
use App\Http\Resources\CatalogoResource;
use App\Models\Catalogo;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    /**
     * Historia #14 – Catálogo General del Almacén.
     * Historia #10 – Alerta de stock crítico (manejada en frontend).
     */
    public function index(): JsonResponse
    {
        $catalogos = Catalogo::with(['activosFijos', 'lotesConsumibles'])->get();

        // Calcular el valor total monetario del inventario (Historia #14)
        $valorTotal = $catalogos->sum(function ($catalogo) {
            if ($catalogo->tipo_registro === 'Serializado') {
                return $catalogo->precio * $catalogo->activosFijos->count();
            }
            return $catalogo->precio * $catalogo->lotesConsumibles->sum('cantidad_disponible');
        });

        return response()->json([
            'data'        => CatalogoResource::collection($catalogos),
            'valor_total' => round($valorTotal, 2),
        ]);
    }

    /**
     * Historia #03 – Ingreso de Nuevo Hardware.
     */
    public function store(StoreCatalogoRequest $request): JsonResponse
    {
        $catalogo = Catalogo::create($request->validated());

        return response()->json([
            'message' => "{$catalogo->categoria} {$catalogo->marca} {$catalogo->modelo} guardado con éxito.",
            'data'    => new CatalogoResource($catalogo),
        ], 201);
    }

    /**
     * Muestra un catálogo específico.
     */
    public function show(string $id): JsonResponse
    {
        $catalogo = Catalogo::with(['activosFijos', 'lotesConsumibles'])->findOrFail($id);

        return response()->json([
            'data' => new CatalogoResource($catalogo),
        ]);
    }

    /**
     * Historia #04 – Edición de Detalles de Componente.
     */
    public function update(UpdateCatalogoRequest $request, string $id): JsonResponse
    {
        $catalogo = Catalogo::findOrFail($id);
        $catalogo->update($request->validated());

        return response()->json([
            'message' => 'Catálogo actualizado con éxito.',
            'data'    => new CatalogoResource($catalogo),
        ]);
    }

    /**
     * Historia #05 – Dar de Baja un Componente (SoftDelete).
     * El registro NO se borra físicamente, solo se marca con deleted_at.
     */
    public function destroy(string $id): JsonResponse
    {
        $catalogo = Catalogo::findOrFail($id);
        $catalogo->delete(); // SoftDelete → marca deleted_at

        return response()->json([
            'message' => "{$catalogo->categoria} {$catalogo->marca} {$catalogo->modelo} dado de baja con éxito.",
        ]);
    }
}
