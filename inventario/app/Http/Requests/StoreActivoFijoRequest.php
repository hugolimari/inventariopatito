<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivoFijoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catalogo_id'  => 'required|exists:catalogos,id',
            'numero_serie' => 'required|string|max:100|unique:activos_fijos,numero_serie',
            'estado'       => 'sometimes|in:En Almacén,Asignado,Dado de Baja,Vendido,Defectuoso',
        ];
    }

    public function messages(): array
    {
        return [
            'catalogo_id.exists'    => 'El catálogo seleccionado no existe.',
            'numero_serie.unique'   => 'Este número de serie ya está registrado.',
        ];
    }
}
