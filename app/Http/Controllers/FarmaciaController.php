<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FarmaciaController extends Controller
{
  public function index()
{
    // El punto indica que 'farmacia' está dentro de la carpeta 'logistica'
    return view('logistica.farmacia');
}
}
