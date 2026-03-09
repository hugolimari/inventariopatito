<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivoFijo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activos_fijos';

    protected $fillable = [
        'catalogo_id',
        'numero_serie',
        'estado',
        'asignado_a',
        'creado_por',
    ];

    // ─── Relaciones ────────────────────────────────────────

    /** Catálogo al que pertenece este activo */
    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    /** Usuario al que está asignado */
    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    /** Usuario que creó el registro */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /** Movimientos Kardex de este activo fijo */
    public function movimientosKardex()
    {
        return $this->hasMany(KardexMovimiento::class, 'activo_fijo_id');
    }
}
