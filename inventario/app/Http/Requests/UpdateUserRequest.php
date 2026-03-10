<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('usuario');

        return [
            'nombre_completo' => 'sometimes|required|string|max:100',
            'username'        => ['sometimes', 'required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'password'        => 'sometimes|nullable|string|min:6',
            'rol'             => 'sometimes|required|in:Admin,Tecnico',
            'turno'           => 'nullable|string|max:50',
        ];
    }
}
