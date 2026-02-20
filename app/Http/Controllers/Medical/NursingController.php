<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NursingController extends Controller
{
    public function index()
{
    return view('medical.enfermeria');
}
}
