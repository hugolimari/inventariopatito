<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nombre_completo',
        'username',
        'password',
        'rol',
        'turno',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // ─── Relaciones ────────────────────────────────────────

    /** Activos fijos creados por este usuario */
    public function activosFijosCreados()
    {
        return $this->hasMany(ActivoFijo::class, 'creado_por');
    }

    /** Activos fijos que tiene asignados */
    public function activosFijosAsignados()
    {
        return $this->hasMany(ActivoFijo::class, 'asignado_a');
    }

    /** Lotes consumibles creados por este usuario */
    public function lotesConsumiblesCreados()
    {
        return $this->hasMany(LoteConsumible::class, 'creado_por');
    }

    /** Movimientos Kardex donde es operador */
    public function movimientosComoOperador()
    {
        return $this->hasMany(KardexMovimiento::class, 'operador_id');
    }

    /** Movimientos Kardex donde es receptor */
    public function movimientosComoReceptor()
    {
        return $this->hasMany(KardexMovimiento::class, 'receptor_id');
    }
}
