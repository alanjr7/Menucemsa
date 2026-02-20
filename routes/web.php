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

    // Rutas para recepción (acceso para admin y reception)
    Route::middleware(['auth', 'role:admin|reception'])->group(function () {
        Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
        Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [\App\Http\Controllers\PatientsController::class, 'create'])->name('patients.create');
        Route::get('/patients/history', [\App\Http\Controllers\PatientsController::class, 'history'])->name('patients.history');
        Route::get('/admision', [ReceptionController::class, 'admision'])->name('admision.index');
        
        // Rutas de admisión/episodios para recepcionistas
        Route::prefix('admision')->name('admision.')->group(function () {
            Route::get('/consulta-externa', [ReceptionController::class, 'consultaExterna'])->name('consulta-externa');
            Route::get('/emergencia', [ReceptionController::class, 'emergencia'])->name('emergencia');
            Route::get('/hospitalizacion', [ReceptionController::class, 'hospitalizacion'])->name('hospitalizacion');
            Route::get('/cirugia', [ReceptionController::class, 'cirugia'])->name('cirugia');
            Route::get('/tipo-pago', [ReceptionController::class, 'tipoPago'])->name('tipo-pago');
            Route::get('/seguros', [ReceptionController::class, 'seguros'])->name('seguros');
            Route::get('/preautorizacion', [ReceptionController::class, 'preautorizacion'])->name('preautorizacion');
        });
        
        // Rutas de emergencias para recepcionistas
        Route::prefix('emergencias-reception')->name('emergencias-reception.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Reception\EmergencyReceptionController::class, 'index'])->name('index');
            Route::get('/regularizar', [\App\Http\Controllers\Reception\EmergencyReceptionController::class, 'regularizar'])->name('regularizar');
            Route::get('/estado', [\App\Http\Controllers\Reception\EmergencyReceptionController::class, 'estado'])->name('estado');
        });
        
        // Rutas de agenda para recepcionistas
        Route::prefix('agenda')->name('agenda.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Reception\AgendaController::class, 'index'])->name('index');
            Route::get('/asignar', [\App\Http\Controllers\Reception\AgendaController::class, 'asignar'])->name('asignar');
            Route::get('/reprogramar', [\App\Http\Controllers\Reception\AgendaController::class, 'reprogramar'])->name('reprogramar');
        });
        
        // Rutas de órdenes de caja para recepcionistas
        Route::prefix('ordenes-caja')->name('ordenes-caja.')->group(function () {
            Route::get('/generar', [\App\Http\Controllers\Reception\OrdenesCajaController::class, 'generar'])->name('generar');
            Route::get('/estado', [\App\Http\Controllers\Reception\OrdenesCajaController::class, 'estado'])->name('estado');
        });
    });

    // Rutas médicas (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/emergencias', [EmergencyController::class, 'index'])->name('emergencias.index');
        Route::get('/enfermeria', [NursingController::class, 'index'])->name('enfermeria.index');
        Route::get('/uti', [UtiController::class, 'index'])->name('uti.index');
        Route::get('/quirofano', [QuirofanoController::class, 'index'])->name('quirofano.index');
        Route::get('/hospitalizacion', [HospitalizacionController::class, 'index'])->name('hospitalizacion.index');
        Route::get('/consulta-externa', function () {
            return view('medical.consulta-externa');
        })->name('consulta.index');
    });

    // Rutas de administración (solo admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
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

    // Rutas gerenciales (solo admin)
    Route::middleware(['role:admin'])->prefix('gerencial')->name('gerencial.')->group(function () {
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
        Route::get('/kpis', [KpiController::class, 'index'])->name('kpis');
    });

    // Rutas de seguridad (solo admin)
    Route::middleware(['role:admin'])->prefix('seguridad')->name('seguridad.')->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/auditoria', [App\Http\Controllers\Seguridad\AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/configuracion', [App\Http\Controllers\Seguridad\ConfiguracionController::class, 'index'])->name('configuracion.index');
    });
});



require __DIR__.'/auth.php';
