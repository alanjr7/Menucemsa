<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReceptionController extends Controller
{
    public function index()
    {
        return view('reception.reception');
    }

    // MODIFICA ESTA FUNCIÓN:
    public function admision(Request $request)
    {
        // 1. Recibimos el paso de la URL (si no hay, por defecto es 1)
        $paso = $request->get('paso', 1);

        // 2. Le pasamos la variable $paso a la vista admision
        return view('reception.admision', compact('paso'));
    }
}
