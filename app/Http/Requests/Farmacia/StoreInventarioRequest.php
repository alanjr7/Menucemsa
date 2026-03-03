<?php

namespace App\Http\Requests\Farmacia;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ID' => 'required|string|max:15',
            'ID_FARMACIA' => 'required|string|max:15|exists:FARMACIA,ID',
            'TIPO_ITEM' => 'nullable|string|max:15',
            'STOCK_MINIMO' => 'nullable|string|max:80',
            'STOCK_DISPONIBLE' => 'nullable|string|max:80',
            'REPOSICION' => 'nullable|string|max:80',
            'FECHA_INGRESO' => 'nullable|date'
        ];
    }

    public function messages()
    {
        return [
            'ID.required' => 'El ID del inventario es obligatorio.',
            'ID.string' => 'El ID debe ser una cadena de texto.',
            'ID.max' => 'El ID no puede tener más de 15 caracteres.',
            'ID_FARMACIA.required' => 'El ID de la farmacia es obligatorio.',
            'ID_FARMACIA.string' => 'El ID de la farmacia debe ser una cadena de texto.',
            'ID_FARMACIA.max' => 'El ID de la farmacia no puede tener más de 15 caracteres.',
            'ID_FARMACIA.exists' => 'La farmacia seleccionada no existe.',
            'TIPO_ITEM.string' => 'El tipo de item debe ser una cadena de texto.',
            'TIPO_ITEM.max' => 'El tipo de item no puede tener más de 15 caracteres.',
            'STOCK_MINIMO.string' => 'El stock mínimo debe ser una cadena de texto.',
            'STOCK_MINIMO.max' => 'El stock mínimo no puede tener más de 80 caracteres.',
            'STOCK_DISPONIBLE.string' => 'El stock disponible debe ser una cadena de texto.',
            'STOCK_DISPONIBLE.max' => 'El stock disponible no puede tener más de 80 caracteres.',
            'REPOSICION.string' => 'La reposición debe ser una cadena de texto.',
            'REPOSICION.max' => 'La reposición no puede tener más de 80 caracteres.',
            'FECHA_INGRESO.date' => 'La fecha de ingreso debe ser una fecha válida.'
        ];
    }
}
