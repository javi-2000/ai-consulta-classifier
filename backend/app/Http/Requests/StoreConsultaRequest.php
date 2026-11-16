<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreConsultaRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'customer_message' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],

        ];
    }


    public function messages(): array
    {
        return [

            'customer_message.required' => 'La consulta es obligatoria',

            'customer_message.min' => 'La consulta debe tener mínimo 10 caracteres',

            'customer_message.max' => 'La consulta no puede superar los 500 caracteres',

        ];
    }

}