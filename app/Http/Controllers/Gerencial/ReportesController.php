<?php

namespace App\Http\Controllers\Gerencial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index()
    {
        return view('gerencial.reportes');
    }
}
