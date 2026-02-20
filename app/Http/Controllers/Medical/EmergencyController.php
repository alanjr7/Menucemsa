<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    // PEGALO AQUÍ ABAJO:
    public function index()
    {
        return view('medical.emergencias');
    }
}
