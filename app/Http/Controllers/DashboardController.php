<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Redirigir según el rol del usuario
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
                
            case 'farmacia':
                return redirect()->route('farmacia.index');
                
            case 'doctor':
                return redirect()->route('medico.dashboard');
                
            case 'reception':
                return redirect()->route('reception');
                
            case 'caja':
                return redirect()->route('caja.operativa.index');
                
            case 'dirmedico':
                return redirect()->route('medico.dashboard');
                
            case 'emergencia':
                return redirect()->route('emergency-staff.dashboard');
                
            case 'gerente':
                return redirect()->route('gerencial.dashboard');
                
            default:
                // Dashboard genérico para roles no definidos
                return view('dashboard');
        }
    }
}
