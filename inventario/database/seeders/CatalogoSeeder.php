<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // Activos Fijos - Computadoras
        Catalogo::create([
            'categoria' => 'Computadoras',
            'marca' => 'Dell',
            'modelo' => 'OptiPlex 7090',
            'tipo_registro' => 'Serializado',
            'precio' => 1200.00,
        ]);

        Catalogo::create([
            'categoria' => 'Computadoras',
            'marca' => 'HP',
            'modelo' => 'ProDesk 400 G9',
            'tipo_registro' => 'Serializado',
            'precio' => 1100.00,
        ]);

        Catalogo::create([
            'categoria' => 'Computadoras',
            'marca' => 'Lenovo',
            'modelo' => 'ThinkCentre M90',
            'tipo_registro' => 'Serializado',
            'precio' => 950.00,
        ]);

        // Activos Fijos - Servidores
        Catalogo::create([
            'categoria' => 'Servidores',
            'marca' => 'Dell',
            'modelo' => 'PowerEdge R750',
            'tipo_registro' => 'Serializado',
            'precio' => 5500.00,
        ]);

        Catalogo::create([
            'categoria' => 'Servidores',
            'marca' => 'HP',
            'modelo' => 'ProLiant DL380 Gen10',
            'tipo_registro' => 'Serializado',
            'precio' => 5200.00,
        ]);

        // Activos Fijos - Impresoras
        Catalogo::create([
            'categoria' => 'Impresoras',
            'marca' => 'Brother',
            'modelo' => 'HL-L8360CDW',
            'tipo_registro' => 'Serializado',
            'precio' => 450.00,
        ]);

        Catalogo::create([
            'categoria' => 'Impresoras',
            'marca' => 'Xerox',
            'modelo' => 'VersaLink C405',
            'tipo_registro' => 'Serializado',
            'precio' => 3200.00,
        ]);

        // Activos Fijos - Monitores
        Catalogo::create([
            'categoria' => 'Monitores',
            'marca' => 'Dell',
            'modelo' => 'U2723DE',
            'tipo_registro' => 'Serializado',
            'precio' => 379.00,
        ]);

        Catalogo::create([
            'categoria' => 'Monitores',
            'marca' => 'LG',
            'modelo' => '27UP550',
            'tipo_registro' => 'Serializado',
            'precio' => 399.00,
        ]);

        // Activos Fijos - Switches
        Catalogo::create([
            'categoria' => 'Equipamiento de Red',
            'marca' => 'Cisco',
            'modelo' => 'Catalyst 2960X-48FPD',
            'tipo_registro' => 'Serializado',
            'precio' => 1800.00,
        ]);

        // Consumibles - Tóner
        Catalogo::create([
            'categoria' => 'Consumibles',
            'marca' => 'Brother',
            'modelo' => 'TN-421BK Tóner Negro',
            'tipo_registro' => 'Consumible',
            'precio' => 45.50,
        ]);

        Catalogo::create([
            'categoria' => 'Consumibles',
            'marca' => 'Brother',
            'modelo' => 'TN-421C Tóner Cyan',
            'tipo_registro' => 'Consumible',
            'precio' => 55.00,
        ]);

        // Consumibles - Papel
        Catalogo::create([
            'categoria' => 'Consumibles',
            'marca' => 'Mondi',
            'modelo' => 'A4 80gsm - Resma 500 hojas',
            'tipo_registro' => 'Consumible',
            'precio' => 4.99,
        ]);

        // Consumibles - Cables
        Catalogo::create([
            'categoria' => 'Consumibles',
            'marca' => 'Belkin',
            'modelo' => 'Cable USB-C 2m',
            'tipo_registro' => 'Consumible',
            'precio' => 12.99,
        ]);

        Catalogo::create([
            'categoria' => 'Consumibles',
            'marca' => 'Cable Matters',
            'modelo' => 'Cable Ethernet Cat6 10m',
            'tipo_registro' => 'Consumible',
            'precio' => 8.99,
        ]);
    }
}
