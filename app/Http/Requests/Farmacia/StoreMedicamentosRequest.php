<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicamentosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'CODIGO' => 'required|string|max:15|unique:MEDICAMENTOS,CODIGO',
            'DESCRIPCION' => 'required|string|max:80',
            'PRECIO' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'CODIGO.required' => 'El código del medicamento es obligatorio.',
            'CODIGO.string' => 'El código debe ser una cadena de texto.',
            'CODIGO.max' => 'El código no puede tener más de 15 caracteres.',
            'CODIGO.unique' => 'El código del medicamento ya existe.',
            'DESCRIPCION.required' => 'La descripción es obligatoria.',
            'DESCRIPCION.string' => 'La descripción debe ser una cadena de texto.',
            'DESCRIPCION.max' => 'La descripción no puede tener más de 80 caracteres.',
            'PRECIO.required' => 'El precio es obligatorio.',
            'PRECIO.numeric' => 'El precio debe ser un valor numérico.',
            'PRECIO.min' => 'El precio no puede ser negativo.'
        ];
    }
}
