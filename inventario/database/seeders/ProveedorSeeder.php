<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        Proveedor::create([
            'nombre_empresa' => 'TechCorp Solutions',
            'telefono' => '+34-912-345-678',
            'marca_principal' => 'Dell',
        ]);

        Proveedor::create([
            'nombre_empresa' => 'Computer Systems Ltd',
            'telefono' => '+34-913-456-789',
            'marca_principal' => 'HP',
        ]);

        Proveedor::create([
            'nombre_empresa' => 'Electronics Distribution',
            'telefono' => '+34-914-567-890',
            'marca_principal' => 'Lenovo',
        ]);

        Proveedor::create([
            'nombre_empresa' => 'Office Equipment Supply',
            'telefono' => '+34-915-678-901',
            'marca_principal' => 'Cisco',
        ]);

        Proveedor::create([
            'nombre_empresa' => 'Consumibles Industriales SA',
            'telefono' => '+34-916-789-012',
            'marca_principal' => 'Brother',
        ]);
    }
}
