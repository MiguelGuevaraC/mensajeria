<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
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
        ];
    }

}
