<?php

namespace Database\Seeders;

use App\Models\ActivoFijo;
use App\Models\KardexMovimiento;
use App\Models\LoteConsumible;
use App\Models\User;
use Illuminate\Database\Seeder;

class KardexMovimientoSeeder extends Seeder
{
    public function run(): void
    {
        $almacenero1 = User::where('username', 'almacenero1')->first();
        $almacenero2 = User::where('username', 'almacenero2')->first();
        $tecnico1 = User::where('username', 'tecnico1')->first();
        $tecnico2 = User::where('username', 'tecnico2')->first();
        $tecnico3 = User::where('username', 'tecnico3')->first();

        // Movimiento de Activo Fijo - Check-out (Asignación)
        $activoFijo1 = ActivoFijo::where('numero_serie', 'DELL-002-2024')->first();
        KardexMovimiento::create([
            'operador_id' => $almacenero1->id,
            'receptor_id' => $tecnico1->id,
            'activo_fijo_id' => $activoFijo1->id,
            'lote_consumible_id' => null,
            'tipo_movimiento' => 'Check-out',
            'cantidad_afectada' => 1,
            'observaciones' => 'Asignación de computadora Dell a técnico Roberto',
        ]);

        // Movimiento de Activo Fijo - Check-in (Retorno)
        $activoFijo2 = ActivoFijo::where('numero_serie', 'HP-001-2024')->first();
        KardexMovimiento::create([
            'operador_id' => $almacenero2->id,
            'receptor_id' => null,
            'activo_fijo_id' => $activoFijo2->id,
            'lote_consumible_id' => null,
            'tipo_movimiento' => 'Check-in',
            'cantidad_afectada' => 1,
            'observaciones' => 'Retorno de computadora HP en buen estado',
        ]);

        // Movimiento de Consumible - Ingreso
        $loteConsumible1 = LoteConsumible::whereHas('catalogo', function($q) {
            $q->where('modelo', 'TN-421BK Tóner Negro');
        })->first();

        KardexMovimiento::create([
            'operador_id' => $almacenero1->id,
            'receptor_id' => null,
            'activo_fijo_id' => null,
            'lote_consumible_id' => $loteConsumible1->id,
            'tipo_movimiento' => 'Ingreso',
            'cantidad_afectada' => 25,
            'observaciones' => 'Ingreso de tóner Negro de proveedor TechCorp',
        ]);

        // Movimiento de Consumible - Check-out (Salida)
        $loteConsumible2 = LoteConsumible::whereHas('catalogo', function($q) {
            $q->where('modelo', 'Cable USB-C 2m');
        })->first();

        if($loteConsumible2) {
            KardexMovimiento::create([
                'operador_id' => $almacenero1->id,
                'receptor_id' => $tecnico2->id,
                'activo_fijo_id' => null,
                'lote_consumible_id' => $loteConsumible2->id,
                'tipo_movimiento' => 'Check-out',
                'cantidad_afectada' => 5,
                'observaciones' => 'Entrega de cables USB-C a técnico Alejandra',
            ]);
        }

        // Movimiento de Consumible - Baja (Ajuste por deterioro)
        $loteConsumible3 = LoteConsumible::whereHas('catalogo', function($q) {
            $q->where('modelo', 'A4 80gsm - Resma 500 hojas');
        })->first();

        if($loteConsumible3) {
            KardexMovimiento::create([
                'operador_id' => $almacenero2->id,
                'receptor_id' => null,
                'activo_fijo_id' => null,
                'lote_consumible_id' => $loteConsumible3->id,
                'tipo_movimiento' => 'Baja',
                'cantidad_afectada' => 10,
                'observaciones' => 'Ajuste por deterioro físico del papel',
            ]);
        }

        // Movimiento de Consumible - Venta (Devolución)
        $loteConsumible4 = LoteConsumible::whereHas('catalogo', function($q) {
            $q->where('modelo', 'Cable Ethernet Cat6 10m');
        })->first();

        if($loteConsumible4) {
            KardexMovimiento::create([
                'operador_id' => $almacenero1->id,
                'receptor_id' => null,
                'activo_fijo_id' => null,
                'lote_consumible_id' => $loteConsumible4->id,
                'tipo_movimiento' => 'Venta',
                'cantidad_afectada' => 5,
                'observaciones' => 'Venta de cables Ethernet por cambio de especificación',
            ]);
        }
    }
}
