<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicamentosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DESCRIPCION' => 'required|string|max:80',
            'PRECIO' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'DESCRIPCION.required' => 'La descripción es obligatoria.',
            'DESCRIPCION.string' => 'La descripción debe ser una cadena de texto.',
            'DESCRIPCION.max' => 'La descripción no puede tener más de 80 caracteres.',
            'PRECIO.required' => 'El precio es obligatorio.',
            'PRECIO.numeric' => 'El precio debe ser un valor numérico.',
            'PRECIO.min' => 'El precio no puede ser negativo.'
        ];
    }
}
