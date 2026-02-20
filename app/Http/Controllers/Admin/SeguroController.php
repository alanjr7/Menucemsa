<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeguroController extends Controller
{
    public function index()
    {
        // Aquí es donde luego traeremos los datos con $seguros = Seguro::all();
        return view('admin.seguros');
    }
}
