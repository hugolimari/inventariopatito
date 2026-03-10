<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
use App\Models\Catalogo;
use App\Models\KardexMovimiento;
use App\Models\LoteConsumible;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Historia #14 – Resumen general del inventario.
     */
    public function index(): JsonResponse
    {
        $totalCatalogos        = Catalogo::count();
        $totalActivosFijos     = ActivoFijo::count();
        $totalLotesConsumibles = LoteConsumible::count();
        $totalMovimientos      = KardexMovimiento::count();

        // Activos fijos por estado
        $activosPorEstado = ActivoFijo::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // Stock crítico: lotes con cantidad < 3 (Historia #10)
        $stockCritico = LoteConsumible::with('catalogo')
            ->where('cantidad_disponible', '<', 3)
            ->count();

        // Valor total del inventario
        $valorTotal = 0;
        Catalogo::with(['activosFijos', 'lotesConsumibles'])->each(function ($catalogo) use (&$valorTotal) {
            if ($catalogo->tipo_registro === 'Serializado') {
                $valorTotal += $catalogo->precio * $catalogo->activosFijos->count();
            } else {
                $valorTotal += $catalogo->precio * $catalogo->lotesConsumibles->sum('cantidad_disponible');
            }
        });

        return response()->json([
            'total_catalogos'         => $totalCatalogos,
            'total_activos_fijos'     => $totalActivosFijos,
            'total_lotes_consumibles' => $totalLotesConsumibles,
            'total_movimientos'       => $totalMovimientos,
            'activos_por_estado'      => $activosPorEstado,
            'stock_critico'           => $stockCritico,
            'valor_total_inventario'  => round($valorTotal, 2),
        ]);
    }
}
