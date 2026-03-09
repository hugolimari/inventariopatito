<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KardexMovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'operador_id'         => $this->operador_id,
            'receptor_id'         => $this->receptor_id,
            'activo_fijo_id'      => $this->activo_fijo_id,
            'lote_consumible_id'  => $this->lote_consumible_id,
            'tipo_movimiento'     => $this->tipo_movimiento,
            'cantidad_afectada'   => $this->cantidad_afectada,
            'observaciones'       => $this->observaciones,
            'created_at'          => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'          => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relaciones condicionales
            'operador' => $this->whenLoaded('operador', fn () => [
                'id'              => $this->operador->id,
                'nombre_completo' => $this->operador->nombre_completo,
            ]),
            'receptor' => $this->whenLoaded('receptor', fn () => [
                'id'              => $this->receptor->id,
                'nombre_completo' => $this->receptor->nombre_completo,
            ]),
            'activo_fijo'      => new ActivoFijoResource($this->whenLoaded('activoFijo')),
            'lote_consumible'  => new LoteConsumibleResource($this->whenLoaded('loteConsumible')),
        ];
    }
}
