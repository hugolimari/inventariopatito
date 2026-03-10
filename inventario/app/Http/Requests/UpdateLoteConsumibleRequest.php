<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoteConsumibleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catalogo_id'         => 'sometimes|required|exists:catalogos,id',
            'cantidad_disponible' => 'sometimes|required|integer|min:0',
        ];
    }
}
