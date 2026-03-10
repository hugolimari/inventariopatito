<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Historia #01 – Login de Usuario
     * Valida credenciales y devuelve un token Sanctum.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token'   => $token,
            'user'    => [
                'id'              => $user->id,
                'nombre_completo' => $user->nombre_completo,
                'username'        => $user->username,
                'rol'             => $user->rol,
                'turno'           => $user->turno,
            ],
        ]);
    }

    /**
     * Cierra la sesión eliminando el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'              => $user->id,
            'nombre_completo' => $user->nombre_completo,
            'username'        => $user->username,
            'rol'             => $user->rol,
            'turno'           => $user->turno,
        ]);
    }
}
