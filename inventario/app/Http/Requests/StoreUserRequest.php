<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:100',
            'username'        => 'required|string|max:50|unique:users,username',
            'password'        => 'required|string|min:6',
            'rol'             => 'required|in:Admin,Tecnico',
            'turno'           => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'  => 'Este nombre de usuario ya está en uso.',
            'rol.in'           => 'El rol debe ser Admin o Tecnico.',
            'password.min'     => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
