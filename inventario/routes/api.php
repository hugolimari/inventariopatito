<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ActivoFijoController;
use App\Http\Controllers\LoteConsumibleController;
use App\Http\Controllers\KardexMovimientoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;

// ─── Rutas Públicas (sin autenticación) ──────────────────
Route::post('/login', [AuthController::class, 'login']);

// ─── Rutas Protegidas ────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Usuarios (gestión por Admin)
    Route::apiResource('usuarios', UserController::class);

    // Catálogo de productos
    Route::apiResource('catalogos', CatalogoController::class);

    // Activos fijos (serializados)
    Route::apiResource('activos-fijos', ActivoFijoController::class);

    // Lotes consumibles
    Route::apiResource('lotes-consumibles', LoteConsumibleController::class);

    // Kardex de movimientos
    Route::apiResource('kardex', KardexMovimientoController::class);

    // Proveedores
    Route::apiResource('proveedores', ProveedorController::class);
});
