<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function index()
    {
        // El nombre debe coincidir exactamente con el archivo .blade.php
        return view('farmacia.ventas');
    }
}
