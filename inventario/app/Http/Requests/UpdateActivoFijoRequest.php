<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivoFijoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $activoId = $this->route('activos_fijo');

        return [
            'catalogo_id'  => 'sometimes|required|exists:catalogos,id',
            'numero_serie' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('activos_fijos', 'numero_serie')->ignore($activoId)],
            'estado'       => 'sometimes|in:En Almacén,Asignado,Dado de Baja,Vendido,Defectuoso',
            'asignado_a'   => 'nullable|exists:users,id',
        ];
    }
}
