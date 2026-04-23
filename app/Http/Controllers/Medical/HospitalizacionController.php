<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Hospitalizacion;
use Illuminate\Http\Request;

class HospitalizacionController extends Controller
{
    public function detalle($id)
    {
        $hospitalizacion = Hospitalizacion::with([
            'paciente', 'medico.user', 'habitacion', 'cama',
            'medicamentosAdministrados.medicamento',
        ])->findOrFail($id);

        return view('medical.internacion-detalle', compact('hospitalizacion'));
    }
}
