<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmaciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ID' => 'required|string|max:15|unique:FARMACIA,ID',
            'DETALLE' => 'required|string|max:80'
        ];
    }

    public function messages()
    {
        return [
            'ID.required' => 'El ID de la farmacia es obligatorio.',
            'ID.string' => 'El ID debe ser una cadena de texto.',
            'ID.max' => 'El ID no puede tener más de 15 caracteres.',
            'ID.unique' => 'El ID de la farmacia ya existe.',
            'DETALLE.required' => 'El detalle es obligatorio.',
            'DETALLE.string' => 'El detalle debe ser una cadena de texto.',
            'DETALLE.max' => 'El detalle no puede tener más de 80 caracteres.'
        ];
    }
}
