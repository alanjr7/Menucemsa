<?php
namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;

class FarmaciaDashboardController extends Controller
{
    public function index() {
        return view('farmacia.index');
    }
}
