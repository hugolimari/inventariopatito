<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;

class ProveedorController extends Controller
{
    /**
     * Historia #12 – Lista todos los proveedores.
     */
    public function index(): JsonResponse
    {
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();

        return response()->json(['data' => $proveedores]);
    }

    /**
     * Historia #12 – Registrar proveedor de hardware.
     */
    public function store(StoreProveedorRequest $request): JsonResponse
    {
        $proveedor = Proveedor::create($request->validated());

        return response()->json([
            'message' => "Proveedor {$proveedor->nombre_empresa} registrado con éxito.",
            'data'    => $proveedor,
        ], 201);
    }

    /**
     * Muestra un proveedor específico.
     */
    public function show(string $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);

        return response()->json(['data' => $proveedor]);
    }

    /**
     * Actualiza un proveedor.
     */
    public function update(UpdateProveedorRequest $request, string $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update($request->validated());

        return response()->json([
            'message' => 'Proveedor actualizado con éxito.',
            'data'    => $proveedor,
        ]);
    }

    /**
     * Elimina un proveedor (borrado físico).
     */
    public function destroy(string $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return response()->json([
            'message' => "Proveedor {$proveedor->nombre_empresa} eliminado con éxito.",
        ]);
    }
}
