<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria'     => 'sometimes|required|string|max:50',
            'marca'         => 'sometimes|required|string|max:50',
            'modelo'        => 'sometimes|required|string|max:100',
            'tipo_registro' => 'sometimes|required|in:Serializado,Consumible',
            'precio'        => 'sometimes|required|numeric|min:0',
        ];
    }
}
