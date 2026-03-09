<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'categoria'     => $this->categoria,
            'marca'         => $this->marca,
            'modelo'        => $this->modelo,
            'tipo_registro' => $this->tipo_registro,
            'precio'        => (float) $this->precio,
            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at'    => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relaciones (solo se cargan si fueron eager-loaded)
            'activos_fijos'      => ActivoFijoResource::collection($this->whenLoaded('activosFijos')),
            'lotes_consumibles'  => LoteConsumibleResource::collection($this->whenLoaded('lotesConsumibles')),
        ];
    }
}
