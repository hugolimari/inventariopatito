<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_empresa'  => 'required|string|max:100',
            'telefono'        => 'required|string|max:20|regex:/^[0-9\+\-\(\)\s]+$/',
            'marca_principal' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'telefono.regex' => 'El formato del teléfono no es válido.',
        ];
    }
}
