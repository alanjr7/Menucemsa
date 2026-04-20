<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Precios de Catering (Comidas)
    |--------------------------------------------------------------------------
    |
    | Precios por tipo de comida para pacientes de internación.
    | Estos precios se usan al generar cargos automáticos.
    |
    */
    'catering' => [
        'precios' => [
            'desayuno' => 15.00,
            'almuerzo' => 25.00,
            'merienda' => 15.00,
            'cena' => 25.00,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Precios de Drenajes
    |--------------------------------------------------------------------------
    |
    | Precios por tipo de drenaje. Se usa el precio del tipo seleccionado
    | o el precio por defecto si no se encuentra el tipo.
    |
    */
    'drenajes' => [
        'precios' => [
            'Pleural' => 50.00,
            'Abdominal' => 60.00,
            'Torácico' => 55.00,
            'General' => 40.00,
        ],
        'precio_default' => 40.00,
    ],
];
