<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKardexMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receptor_id'        => 'nullable|exists:users,id',
            'activo_fijo_id'     => 'nullable|exists:activos_fijos,id|required_without:lote_consumible_id',
            'lote_consumible_id' => 'nullable|exists:lotes_consumibles,id|required_without:activo_fijo_id',
            'tipo_movimiento'    => 'required|in:Ingreso,Check-out,Check-in,Baja,Venta,RMA',
            'cantidad_afectada'  => 'required|integer|min:1',
            'observaciones'      => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'activo_fijo_id.required_without'     => 'Debe seleccionar un activo fijo o un lote consumible.',
            'lote_consumible_id.required_without'  => 'Debe seleccionar un activo fijo o un lote consumible.',
            'cantidad_afectada.min'                => 'La cantidad debe ser mayor a 0.',
            'tipo_movimiento.in'                   => 'Tipo de movimiento no válido.',
        ];
    }
}
