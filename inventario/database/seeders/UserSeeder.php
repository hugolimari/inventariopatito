<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'nombre_completo' => 'Juan Administrador',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'rol' => 'Admin',
            'turno' => 'matutino',
        ]);

        // Técnicos - Almacenero 1
        User::create([
            'nombre_completo' => 'Carlos Almacenero',
            'username' => 'almacenero1',
            'password' => Hash::make('password123'),
            'rol' => 'Tecnico',
            'turno' => 'matutino',
        ]);

        // Técnico - Almacenero 2
        User::create([
            'nombre_completo' => 'Patricia Almacenera',
            'username' => 'almacenero2',
            'password' => Hash::make('password123'),
            'rol' => 'Tecnico',
            'turno' => 'vespertino',
        ]);

        // Técnico 1
        User::create([
            'nombre_completo' => 'Roberto Técnico',
            'username' => 'tecnico1',
            'password' => Hash::make('password123'),
            'rol' => 'Tecnico',
            'turno' => 'matutino',
        ]);

        // Técnico 2
        User::create([
            'nombre_completo' => 'Alejandra Técnica',
            'username' => 'tecnico2',
            'password' => Hash::make('password123'),
            'rol' => 'Tecnico',
            'turno' => 'vespertino',
        ]);

        // Técnico 3
        User::create([
            'nombre_completo' => 'Luis Técnico',
            'username' => 'tecnico3',
            'password' => Hash::make('password123'),
            'rol' => 'Tecnico',
            'turno' => 'nocturno',
        ]);
    }
}
