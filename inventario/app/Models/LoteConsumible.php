<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoteConsumible extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lotes_consumibles';

    protected $fillable = [
        'catalogo_id',
        'cantidad_disponible',
        'creado_por',
    ];

    // ─── Relaciones ────────────────────────────────────────

    /** Catálogo al que pertenece este lote */
    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    /** Usuario que creó el lote */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /** Movimientos Kardex de este lote consumible */
    public function movimientosKardex()
    {
        return $this->hasMany(KardexMovimiento::class, 'lote_consumible_id');
    }
}
