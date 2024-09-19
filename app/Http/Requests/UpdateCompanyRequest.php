<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function rules()
    {
        $companyId = $this->route('id'); // Obtener el ID de la empresa desde la ruta

        return [
            'typeOfDocument' => 'nullable|string|max:50',
            'documentNumber' => [
                'required',
                'string',
                'max:20',
                Rule::unique('companies', 'documentNumber')->ignore($companyId),
            ],
            'businessName' => [
                'required',
                'string',
                Rule::unique('companies', 'businessName')->ignore($companyId),
            ],
            'tradeName' => 'nullable|string|max:255',
            'representativeName' => 'required|string|max:255',
            'representativeDni' => 'nullable|string|max:20',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'costSend' => 'required|numeric|gte:0.0',
        ];
    }

    public function messages()
    {
        return [
            'typeOfDocument.string' => 'El tipo de documento debe ser una cadena de texto.',
            'typeOfDocument.max' => 'El tipo de documento no debe superar los 50 caracteres.',
            'documentNumber.required' => 'El número de documento es obligatorio.',
            'documentNumber.string' => 'El número de documento debe ser una cadena de texto.',
            'documentNumber.max' => 'El número de documento no debe superar los 20 caracteres.',
            'documentNumber.unique' => 'El número de documento ya está registrado.',
            'businessName.required' => 'El nombre de la empresa es obligatorio.',
            'businessName.string' => 'El nombre de la empresa debe ser una cadena de texto.',
            'businessName.unique' => 'El nombre de la empresa ya está registrado.',
            'tradeName.string' => 'El nombre comercial debe ser una cadena de texto.',
            'tradeName.max' => 'El nombre comercial no debe superar los 255 caracteres.',
            'representativeName.required' => 'El nombre del representante es obligatorio.',
            'representativeName.string' => 'El nombre del representante debe ser una cadena de texto.',
            'representativeName.max' => 'El nombre del representante no debe superar los 255 caracteres.',
            'representativeDni.string' => 'El DNI del representante debe ser una cadena de texto.',
            'representativeDni.max' => 'El DNI del representante no debe superar los 20 caracteres.',
            'telephone.string' => 'El teléfono debe ser una cadena de texto.',
            'telephone.max' => 'El teléfono no debe superar los 20 caracteres.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no debe superar los 255 caracteres.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no debe superar los 255 caracteres.',
            'status.boolean' => 'El estado debe ser un valor booleano.',
            'costSend.required' => 'El costo de envío es obligatorio.',
            'costSend' => 'required|numeric|gte:0.0000',
            'costSend.gte' => 'El valor de :attribute debe ser mayor o igual a 0.0.',

        ];

    }

}
