<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactByGroupRequest extends FormRequest
{


    public function rules()
    {
        return [
            'documentNumber' => 'nullable|numeric', // Número de documento obligatorio, numérico, y con 8 dígitos.
            'names'          => 'required|string|max:255',   // Nombres obligatorios, string, máximo 255 caracteres.
            'telephone'      => 'required|numeric|digits:9', // Teléfono obligatorio, numérico, y con 9 dígitos.
            'address'        => 'nullable|string|max:255',   // Dirección obligatoria, string, máximo 255 caracteres.
            'concept'        => 'nullable|string|max:255',   // Concepto obligatorio, string, máximo 255 caracteres.
            'amount'         => 'nullable|numeric|min:0',    // Monto obligatorio, numérico, no puede ser menor a 0.
            'dateReference'  => 'nullable|date',             // Fecha de referencia obligatoria y formato de fecha.
        ];
    }
    public function messages()
{
    return [
        'documentNumber.required' => 'El número de documento es obligatorio.',
        'documentNumber.numeric'  => 'El número de documento debe ser numérico.',
        
        'names.required'          => 'El nombre es obligatorio.',
        'names.string'            => 'El nombre debe ser una cadena de texto.',
        'names.max'               => 'El nombre no puede exceder los 255 caracteres.',
        
        'telephone.required'      => 'El teléfono es obligatorio.',
        'telephone.numeric'       => 'El teléfono debe ser numérico.',
        'telephone.digits'        => 'El teléfono debe tener exactamente 9 dígitos.',
        
        'address.required'        => 'La dirección es obligatoria.',
        'address.string'          => 'La dirección debe ser una cadena de texto.',
        'address.max'             => 'La dirección no puede exceder los 255 caracteres.',
        
        'concept.required'        => 'El concepto es obligatorio.',
        'concept.string'          => 'El concepto debe ser una cadena de texto.',
        'concept.max'             => 'El concepto no puede exceder los 255 caracteres.',
        
        'amount.required'         => 'El monto es obligatorio.',
        'amount.numeric'          => 'El monto debe ser un valor numérico.',
        'amount.min'              => 'El monto no puede ser negativo.',
        
        'dateReference.required'  => 'La fecha de referencia es obligatoria.',
        'dateReference.date'      => 'La fecha de referencia debe ser una fecha válida.',
    ];
}

}
