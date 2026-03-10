<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\LoteConsumible;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoteConsumibleSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo_toner_negro = Catalogo::where('modelo', 'TN-421BK Tóner Negro')->first();
        $catalogo_toner_cyan = Catalogo::where('modelo', 'TN-421C Tóner Cyan')->first();
        $catalogo_papel = Catalogo::where('modelo', 'A4 80gsm - Resma 500 hojas')->first();
        $catalogo_usb = Catalogo::where('modelo', 'Cable USB-C 2m')->first();
        $catalogo_ethernet = Catalogo::where('modelo', 'Cable Ethernet Cat6 10m')->first();

        $almacenero = User::where('username', 'almacenero1')->first();
        $admin = User::where('username', 'admin')->first();

        // Tóner Negro
        LoteConsumible::create([
            'catalogo_id' => $catalogo_toner_negro->id,
            'cantidad_disponible' => 25,
            'creado_por' => $almacenero->id,
        ]);

        // Tóner Cyan
        LoteConsumible::create([
            'catalogo_id' => $catalogo_toner_cyan->id,
            'cantidad_disponible' => 15,
            'creado_por' => $almacenero->id,
        ]);

        // Papel A4
        LoteConsumible::create([
            'catalogo_id' => $catalogo_papel->id,
            'cantidad_disponible' => 100,
            'creado_por' => $admin->id,
        ]);

        // Cable USB-C
        LoteConsumible::create([
            'catalogo_id' => $catalogo_usb->id,
            'cantidad_disponible' => 50,
            'creado_por' => $almacenero->id,
        ]);

        // Cable Ethernet
        LoteConsumible::create([
            'catalogo_id' => $catalogo_ethernet->id,
            'cantidad_disponible' => 75,
            'creado_por' => $almacenero->id,
        ]);
    }
}
