<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
        ];
    }
}
