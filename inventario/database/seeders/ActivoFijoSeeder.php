<?php

namespace Database\Seeders;

use App\Models\ActivoFijo;
use App\Models\Catalogo;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivoFijoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo_computadora_dell = Catalogo::where('marca', 'Dell')
            ->where('modelo', 'OptiPlex 7090')
            ->first();
        $catalogo_computadora_hp = Catalogo::where('marca', 'HP')
            ->where('modelo', 'ProDesk 400 G9')
            ->first();
        $catalogo_servidor_dell = Catalogo::where('marca', 'Dell')
            ->where('modelo', 'PowerEdge R750')
            ->first();
        $catalogo_monitor_dell = Catalogo::where('marca', 'Dell')
            ->where('modelo', 'U2723DE')
            ->first();
        $catalogo_impresora = Catalogo::where('marca', 'Brother')
            ->where('modelo', 'HL-L8360CDW')
            ->first();
        $catalogo_switch = Catalogo::where('marca', 'Cisco')
            ->where('modelo', 'Catalyst 2960X-48FPD')
            ->first();

        $almacenero = User::where('username', 'almacenero1')->first();
        $admin = User::where('username', 'admin')->first();
        $tecnico1 = User::where('username', 'tecnico1')->first();
        $tecnico2 = User::where('username', 'tecnico2')->first();

        // Computadoras
        ActivoFijo::create([
            'catalogo_id' => $catalogo_computadora_dell->id,
            'numero_serie' => 'DELL-001-2024',
            'estado' => 'En Almacén',
            'asignado_a' => null,
            'creado_por' => $almacenero->id,
        ]);

        ActivoFijo::create([
            'catalogo_id' => $catalogo_computadora_dell->id,
            'numero_serie' => 'DELL-002-2024',
            'estado' => 'Asignado',
            'asignado_a' => $tecnico1->id,
            'creado_por' => $almacenero->id,
        ]);

        ActivoFijo::create([
            'catalogo_id' => $catalogo_computadora_hp->id,
            'numero_serie' => 'HP-001-2024',
            'estado' => 'Asignado',
            'asignado_a' => $tecnico2->id,
            'creado_por' => $almacenero->id,
        ]);

        // Servidor
        ActivoFijo::create([
            'catalogo_id' => $catalogo_servidor_dell->id,
            'numero_serie' => 'DELL-SRV-001-2023',
            'estado' => 'En Almacén',
            'asignado_a' => null,
            'creado_por' => $admin->id,
        ]);

        // Monitores
        ActivoFijo::create([
            'catalogo_id' => $catalogo_monitor_dell->id,
            'numero_serie' => 'DELL-MON-001-2024',
            'estado' => 'Asignado',
            'asignado_a' => $tecnico1->id,
            'creado_por' => $almacenero->id,
        ]);

        ActivoFijo::create([
            'catalogo_id' => $catalogo_monitor_dell->id,
            'numero_serie' => 'DELL-MON-002-2024',
            'estado' => 'En Almacén',
            'asignado_a' => null,
            'creado_por' => $almacenero->id,
        ]);

        // Impresora
        ActivoFijo::create([
            'catalogo_id' => $catalogo_impresora->id,
            'numero_serie' => 'BROTHER-IMP-001-2024',
            'estado' => 'En Almacén',
            'asignado_a' => null,
            'creado_por' => $almacenero->id,
        ]);

        // Switch
        ActivoFijo::create([
            'catalogo_id' => $catalogo_switch->id,
            'numero_serie' => 'CISCO-SW-001-2023',
            'estado' => 'En Almacén',
            'asignado_a' => null,
            'creado_por' => $admin->id,
        ]);
    }
}
