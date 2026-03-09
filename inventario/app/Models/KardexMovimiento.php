<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexMovimiento extends Model
{
    use HasFactory;

    protected $table = 'kardex_movimientos';

    protected $fillable = [
        'operador_id',
        'receptor_id',
        'activo_fijo_id',
        'lote_consumible_id',
        'tipo_movimiento',
        'cantidad_afectada',
        'observaciones',
    ];

    // ─── Relaciones ────────────────────────────────────────

    /** Usuario que realizó la operación (Almacenero) */
    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    /** Técnico que recibe el hardware (puede ser null) */
    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }

    /** Activo fijo afectado (puede ser null si es consumible) */
    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class, 'activo_fijo_id');
    }

    /** Lote consumible afectado (puede ser null si es serializado) */
    public function loteConsumible()
    {
        return $this->belongsTo(LoteConsumible::class, 'lote_consumible_id');
    }
}
