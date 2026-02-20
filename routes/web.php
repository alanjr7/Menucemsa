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

    Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
    Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
    Route::get('/admision', [ReceptionController::class, 'admision'])->name('admision.index');

    Route::get('/emergencias', [EmergencyController::class, 'index'])->name('emergencias.index');

    Route::get('/enfermeria', [NursingController::class, 'index'])->name('enfermeria.index');

    Route::get('/uti', [UtiController::class, 'index'])->name('uti.index');

    Route::get('/quirofano', [QuirofanoController::class, 'index'])->name('quirofano.index');

    Route::get('/hospitalizacion', [HospitalizacionController::class, 'index'])->name('hospitalizacion.index');

    Route::get('/consulta-externa', function () {
        return view('medical.consulta-externa');
    })->name('consulta.index');

    Route::get('/admin/caja', function () {
        return view('admin.caja');
    })->name('caja.index');


    Route::get('/admin/facturacion', function () {
    return view('admin.facturacion');
})->name('facturacion.index');

    Route::get('/admin/tarifarios', function () {
        return view('admin.tarifarios');
    })->name('admin.tarifarios');

Route::get('/admin/seguros', [SeguroController::class, 'index'])->name('admin.seguros');

Route::get('/admin/cuentas-por-cobrar', [CuentaCobrarController::class, 'index'])->name('admin.cuentas');

Route::get('/farmacia', [FarmaciaController::class, 'index'])->name('farmacia.index');

Route::get('/gerencial/reportes', [ReportesController::class, 'index'])->name('gerencial.reportes');
Route::get('/gerencial/kpis', [KpiController::class, 'index'])->name('gerencial.kpis');

Route::prefix('seguridad')->name('seguridad.')->group(function () {
    // Aquí definimos la ruta 'usuarios.index', que al estar en el grupo será 'seguridad.usuarios.index'
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
     Route::get('/auditoria', [App\Http\Controllers\Seguridad\AuditoriaController::class, 'index'])->name('auditoria.index');
   Route::get('/configuracion', [App\Http\Controllers\Seguridad\ConfiguracionController::class, 'index'])->name('configuracion.index');

     });

    });



require __DIR__.'/auth.php';
