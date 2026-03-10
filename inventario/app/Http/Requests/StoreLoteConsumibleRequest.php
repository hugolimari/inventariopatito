<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoteConsumibleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catalogo_id'         => 'required|exists:catalogos,id',
            'cantidad_disponible' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'catalogo_id.exists'         => 'El catálogo seleccionado no existe.',
            'cantidad_disponible.min'    => 'La cantidad debe ser al menos 1.',
        ];
    }
}
