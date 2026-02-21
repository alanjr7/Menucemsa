<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\Medical\EmergencyController;
use App\Http\Controllers\Medical\NursingController;
use App\Http\Controllers\Medical\UtiController;
use App\Http\Controllers\Medical\QuirofanoController;
use App\Http\Controllers\Medical\HospitalizacionController;
use App\Http\Controllers\Admin\SeguroController;
use App\Http\Controllers\Admin\CuentaCobrarController;
use App\Http\Controllers\FarmaciaController;

use App\Http\Controllers\Gerencial\ReportesController;
use App\Http\Controllers\Gerencial\KpiController;
use App\Http\Controllers\Seguridad\UsuariosController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ActivityLogController;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para recepción y pacientes (acceso para admin, reception y dirmedico)
    Route::middleware(['auth', 'role:admin|reception|dirmedico'])->group(function () {
        Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
        Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
        Route::get('/admision', [ReceptionController::class, 'admision'])->name('admision.index');
    });

    // Rutas médicas (admin y dirmedico)
    Route::middleware(['role:admin|dirmedico'])->group(function () {
        Route::get('/enfermeria', [NursingController::class, 'index'])->name('enfermeria.index');
        Route::get('/uti', [UtiController::class, 'index'])->name('uti.index');
        Route::get('/quirofano', [QuirofanoController::class, 'index'])->name('quirofano.index');
        Route::get('/hospitalizacion', [HospitalizacionController::class, 'index'])->name('hospitalizacion.index');
        Route::get('/consulta-externa', function () {
            return view('medical.consulta-externa');
        })->name('consulta.index');
    });

    // Rutas de emergencia (admin, dirmedico y emergencia)
    Route::middleware(['role:admin|dirmedico|emergencia'])->group(function () {
        Route::get('/emergencias', [EmergencyController::class, 'index'])->name('emergencias.index');
    });

    // Rutas de administración (admin y caja)
    Route::middleware(['role:admin|caja'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/caja', function () {
            return view('admin.caja');
        })->name('caja.index');
        
        Route::get('/facturacion', function () {
            return view('admin.facturacion');
        })->name('facturacion.index');
        
        Route::get('/tarifarios', function () {
            return view('admin.tarifarios');
        })->name('tarifarios');
        
        Route::get('/seguros', [SeguroController::class, 'index'])->name('seguros');
        Route::get('/cuentas-por-cobrar', [CuentaCobrarController::class, 'index'])->name('cuentas');
    });

    // Rutas de farmacia (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/farmacia', [FarmaciaController::class, 'index'])->name('farmacia.index');
    });

    // Rutas gerenciales (admin y gerente)
    Route::middleware(['role:admin|gerente'])->prefix('gerencial')->name('gerencial.')->group(function () {
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
        Route::get('/kpis', [KpiController::class, 'index'])->name('kpis');
    });

    // Rutas de seguridad (admin y gerente)
    Route::middleware(['role:admin|gerente'])->prefix('seguridad')->name('seguridad.')->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/auditoria', [App\Http\Controllers\Seguridad\AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/configuracion', [App\Http\Controllers\Seguridad\ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::get('/bitacora', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // Rutas de gestión de usuarios (admin y gerente)
    Route::middleware(['role:admin|gerente'])->prefix('user-management')->name('user-management.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });
});



require __DIR__.'/auth.php';
