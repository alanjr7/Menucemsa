<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class StoreInsumosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'CODIGO' => 'required|string|max:15|unique:INSUMOS,CODIGO',
            'NOMBRE' => 'required|string|max:80',
            'DESCRIPCION' => 'nullable|string|max:80'
        ];
    }

    public function messages()
    {
        return [
            'CODIGO.required' => 'El código del insumo es obligatorio.',
            'CODIGO.string' => 'El código debe ser una cadena de texto.',
            'CODIGO.max' => 'El código no puede tener más de 15 caracteres.',
            'CODIGO.unique' => 'El código del insumo ya existe.',
            'NOMBRE.required' => 'El nombre es obligatorio.',
            'NOMBRE.string' => 'El nombre debe ser una cadena de texto.',
            'NOMBRE.max' => 'El nombre no puede tener más de 80 caracteres.',
            'DESCRIPCION.string' => 'La descripción debe ser una cadena de texto.',
            'DESCRIPCION.max' => 'La descripción no puede tener más de 80 caracteres.'
        ];
    }
}
