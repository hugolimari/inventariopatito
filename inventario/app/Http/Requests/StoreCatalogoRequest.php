<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria'     => 'required|string|max:50',
            'marca'         => 'required|string|max:50',
            'modelo'        => 'required|string|max:100',
            'tipo_registro' => 'required|in:Serializado,Consumible',
            'precio'        => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_registro.in' => 'El tipo de registro debe ser Serializado o Consumible.',
            'precio.min'       => 'El precio no puede ser negativo.',
        ];
    }
}
