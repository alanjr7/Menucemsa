<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class StoreCajaFarmaciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'CODIGO' => 'required|string|max:15|unique:CAJA_FARMACIA,CODIGO',
            'DETALLE' => 'nullable|string|max:80',
            'TOTAL' => 'nullable|numeric|min:0',
            'ID_CAJA' => 'nullable|string|max:15|exists:CAJA,ID'
        ];
    }

    public function messages()
    {
        return [
            'CODIGO.required' => 'El código de la caja es obligatorio.',
            'CODIGO.string' => 'El código debe ser una cadena de texto.',
            'CODIGO.max' => 'El código no puede tener más de 15 caracteres.',
            'CODIGO.unique' => 'El código de la caja ya existe.',
            'DETALLE.string' => 'El detalle debe ser una cadena de texto.',
            'DETALLE.max' => 'El detalle no puede tener más de 80 caracteres.',
            'TOTAL.numeric' => 'El total debe ser un valor numérico.',
            'TOTAL.min' => 'El total no puede ser negativo.',
            'ID_CAJA.string' => 'El ID de la caja debe ser una cadena de texto.',
            'ID_CAJA.max' => 'El ID de la caja no puede tener más de 15 caracteres.',
            'ID_CAJA.exists' => 'La caja seleccionada no existe.'
        ];
    }
}
