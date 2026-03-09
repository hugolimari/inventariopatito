<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalogo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'catalogos';

    protected $fillable = [
        'categoria',
        'marca',
        'modelo',
        'tipo_registro',
        'precio',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
        ];
    }

    // ─── Relaciones ────────────────────────────────────────

    /** Activos fijos (serializados) que pertenecen a este catálogo */
    public function activosFijos()
    {
        return $this->hasMany(ActivoFijo::class, 'catalogo_id');
    }

    /** Lotes consumibles que pertenecen a este catálogo */
    public function lotesConsumibles()
    {
        return $this->hasMany(LoteConsumible::class, 'catalogo_id');
    }
}
