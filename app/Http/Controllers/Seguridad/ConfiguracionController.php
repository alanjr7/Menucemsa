<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('seguridad.configuracion');
    }
}
