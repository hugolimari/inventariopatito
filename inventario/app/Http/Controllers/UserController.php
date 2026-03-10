<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Historia #02 / #11 – Lista todos los usuarios.
     */
    public function index(): JsonResponse
    {
        $usuarios = User::orderBy('nombre_completo')->get();

        return response()->json(['data' => $usuarios->map(fn ($u) => [
            'id'              => $u->id,
            'nombre_completo' => $u->nombre_completo,
            'username'        => $u->username,
            'rol'             => $u->rol,
            'turno'           => $u->turno,
            'created_at'      => $u->created_at?->format('Y-m-d H:i:s'),
        ])]);
    }

    /**
     * Historia #02 – Registrar nuevo usuario (Almacenero o Técnico).
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => "Usuario {$user->nombre_completo} registrado con éxito.",
            'data'    => $user,
        ], 201);
    }

    /**
     * Muestra un usuario específico.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json(['data' => [
            'id'              => $user->id,
            'nombre_completo' => $user->nombre_completo,
            'username'        => $user->username,
            'rol'             => $user->rol,
            'turno'           => $user->turno,
            'created_at'      => $user->created_at?->format('Y-m-d H:i:s'),
        ]]);
    }

    /**
     * Actualiza los datos de un usuario.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        return response()->json([
            'message' => 'Usuario actualizado con éxito.',
            'data'    => $user,
        ]);
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado con éxito.',
        ]);
    }
}
