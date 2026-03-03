<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmaciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DETALLE' => 'required|string|max:80'
        ];
    }

    public function messages()
    {
        return [
            'DETALLE.required' => 'El detalle es obligatorio.',
            'DETALLE.string' => 'El detalle debe ser una cadena de texto.',
            'DETALLE.max' => 'El detalle no puede tener más de 80 caracteres.'
        ];
    }
}
