<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuirofanoController extends Controller
{
    public function index()
    {
        return view('medical.quirofano');
    }
}
