<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInsumosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'NOMBRE' => 'required|string|max:80',
            'DESCRIPCION' => 'nullable|string|max:80'
        ];
    }

    public function messages()
    {
        return [
            'NOMBRE.required' => 'El nombre es obligatorio.',
            'NOMBRE.string' => 'El nombre debe ser una cadena de texto.',
            'NOMBRE.max' => 'El nombre no puede tener más de 80 caracteres.',
            'DESCRIPCION.string' => 'La descripción debe ser una cadena de texto.',
            'DESCRIPCION.max' => 'La descripción no puede tener más de 80 caracteres.'
        ];
    }
}
