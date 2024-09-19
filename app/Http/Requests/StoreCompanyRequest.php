<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{

    public function rules()
    {
        return [
            'typeOfDocument' => 'nullable|string|max:50',
            'documentNumber' => 'required|string|max:20|unique:companies,documentNumber',
            'businessName' => 'required|string',
            'tradeName' => 'required|string',
            'representativeName' => 'required|string',
            'representativeDni' => 'nullable|string|max:20',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|string|email',
            'address' => 'nullable|string',
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
            'tradeName.required' => 'El nombre comercial es obligatorio.',
            'tradeName.string' => 'El nombre comercial debe ser una cadena de texto.',
            'representativeName.required' => 'El nombre del representante es obligatorio.',
            'representativeName.string' => 'El nombre del representante debe ser una cadena de texto.',
            'representativeDni.string' => 'El DNI del representante debe ser una cadena de texto.',
            'representativeDni.max' => 'El DNI del representante no debe superar los 20 caracteres.',
            'telephone.string' => 'El teléfono debe ser una cadena de texto.',
            'telephone.max' => 'El teléfono no debe superar los 20 caracteres.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'costSend.required' => 'El costo de envío es obligatorio.',
            'costSend' => 'required|numeric|gte:0.0000',
            'costSend.gte' => 'El valor de :attribute debe ser mayor o igual a 0.0000.',

        ];
    }

}
