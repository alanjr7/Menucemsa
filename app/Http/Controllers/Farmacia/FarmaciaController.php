<?php

namespace App\Http\Controllers\Farmacia;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FarmaciaController extends Controller
{
    public function index()
    {
        // Si moviste el archivo a la carpeta farmacia, usa 'farmacia.index'
        // Si sigue en logistica, déjalo como estaba
        return view('farmacia.index');
    }

    // Método para el nuevo botón "Punto de Venta"
    public function pos()
    {
        return view('farmacia.pos');
    }
}
