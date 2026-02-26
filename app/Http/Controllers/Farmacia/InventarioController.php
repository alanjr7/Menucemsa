<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        // Esta función cargará la página del inventario
        return view('farmacia.inventario');
    }
}
