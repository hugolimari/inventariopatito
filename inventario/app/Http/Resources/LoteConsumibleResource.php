<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoteConsumibleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'catalogo_id'          => $this->catalogo_id,
            'cantidad_disponible'  => $this->cantidad_disponible,
            'creado_por'           => $this->creado_por,
            'created_at'           => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'           => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at'           => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relaciones condicionales
            'catalogo'        => new CatalogoResource($this->whenLoaded('catalogo')),
            'usuario_creador' => $this->whenLoaded('creadoPor', fn () => [
                'id'              => $this->creadoPor->id,
                'nombre_completo' => $this->creadoPor->nombre_completo,
            ]),
        ];
    }
}
