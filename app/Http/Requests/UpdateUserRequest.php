<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
  
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'typeofUser_id' => 'required|exists:type_users,id',
            'username' => [
                'required',
                'string',
                'min:4',
                'max:30',
            ],
            'password' => [
                'required', // La contraseña es opcional en la actualización
                'string',
                'min:8',
                'max:30',
                'regex:/[A-Z]/', // Al menos una letra mayúscula
                'regex:/[a-z]/', // Al menos una letra minúscula
                'regex:/[0-9]/', // Al menos un número
            ],
        ];
    }
    public function messages()
    {
        return [
            'company_id.required' => 'El campo empresa es obligatorio.',
            'company_id.exists' => 'La empresa seleccionada no es válida.',
            'typeofUser_id.required' => 'El campo tipo de usuario es obligatorio.',
            'typeofUser_id.exists' => 'El tipo de usuario seleccionado no es válido.',
            'username.required' => 'El campo nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 4 caracteres.',
            'username.max' => 'El nombre de usuario no puede exceder los 30 caracteres.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 30 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número.',
        ];
    }
}
