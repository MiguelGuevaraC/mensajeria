<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupSendRequest extends FormRequest
{
    public function rules()
    {
        return [
       
            'name' => [
                'required',
                'string',
            ],
            'comment' => [
                'required',
                'string',
            ],
        ];
    }
    
    public function messages()
    {
        return [

            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'comment.required' => 'El campo comentario es obligatorio.',
            'comment.string' => 'El campo comentario debe ser una cadena de texto.',
        ];
    }
    
}
