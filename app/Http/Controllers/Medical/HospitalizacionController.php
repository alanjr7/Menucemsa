<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HospitalizacionController extends Controller
{
    public function index()
    {
        // Esto busca el archivo en resources/views/medical/hospitalizacion.blade.php
        return view('medical.hospitalizacion');
    }
}
