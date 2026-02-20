<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index()
    {
        return view('seguridad.auditoria');
    }
}
