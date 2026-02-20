<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GerencialController extends Controller
{
    public function reportes()
    {
        // Retornamos la vista dentro de una nueva carpeta Gerencial
        return view('gerencial.reportes');
    }
}
