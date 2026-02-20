<?php

namespace App\Http\Controllers\Gerencial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        return view('gerencial.kpis');
    }
}
