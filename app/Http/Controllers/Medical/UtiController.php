<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UtiController extends Controller
{
    public function index()
    {
        // Aquí luego buscarás los pacientes críticos de la BD
        return view('medical.uti');
    }
}
