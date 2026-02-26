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


use App\Http\Controllers\Gerencial\ReportesController;
use App\Http\Controllers\Gerencial\KpiController;
use App\Http\Controllers\Seguridad\UsuariosController;
use App\Http\Controllers\Farmacia\FarmaciaDashboardController;
use App\Http\Controllers\Farmacia\PuntoVentaController;
use App\Http\Controllers\Farmacia\InventarioController;
use App\Http\Controllers\Farmacia\VentasController;
use App\Http\Controllers\Farmacia\ClientesController;
use App\Http\Controllers\Farmacia\ReporteController;

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
// Rutas de farmacia organizadas por controlador modular
Route::middleware(['auth', 'role:admin'])->prefix('farmacia')->name('farmacia.')->group(function () {

    // URL: /farmacia -> Llama a FarmaciaDashboardController
    Route::get('/', [FarmaciaDashboardController::class, 'index'])->name('index');

    // URL: /farmacia/punto-de-venta -> Llama a PuntoVentaController
    Route::get('/punto-de-venta', [PuntoVentaController::class, 'index'])->name('pos');

    // NUEVA RUTA: URL: /farmacia/inventario -> Llama a InventarioController
    // El nombre de la ruta será 'farmacia.inventario' automáticamente por el name('farmacia.')
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');

       Route::get('/ventas', [VentasController::class, 'index'])->name('ventas');

         Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes');

         Route ::get('/reporte', [ReporteController::class, 'index'])->name('reporte');

         });


});



require __DIR__.'/auth.php';
