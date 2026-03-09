<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\Reception\ConsultaExternaController;
use App\Http\Controllers\Reception\EmergenciaController;
use App\Http\Controllers\Reception\HospitalizacionController;
use App\Http\Controllers\Medical\EmergencyController;
use App\Http\Controllers\Medical\NursingController;
use App\Http\Controllers\Medical\UtiController;
use App\Http\Controllers\Medical\QuirofanoController;
use App\Http\Controllers\Medical\HospitalizacionController as MedicalHospitalizacionController;
use App\Http\Controllers\Admin\SeguroController;
use App\Http\Controllers\Admin\CuentaCobrarController;


use App\Http\Controllers\Admin\TarifarioController;
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

    // Rutas para recepción y pacientes (acceso para admin, reception y dirmedico)
    Route::middleware(['auth', 'role:admin|reception|dirmedico'])->group(function () {
        Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
        Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
        Route::get('/admision', [ReceptionController::class, 'admision'])->name('admision.index');
        
        // Rutas para las nuevas páginas separadas
        Route::get('/reception/consulta-externa', [ConsultaExternaController::class, 'index'])->name('reception.consulta-externa');
        Route::get('/reception/emergencia', [EmergenciaController::class, 'index'])->name('reception.emergencia');
        Route::get('/reception/hospitalizacion', [HospitalizacionController::class, 'index'])->name('reception.hospitalizacion');
        
        // Rutas API para consulta externa
        Route::post('/api/buscar-paciente', [ConsultaExternaController::class, 'buscarPaciente'])->name('reception.buscar-paciente');
        Route::post('/api/registrar-consulta-externa', [ConsultaExternaController::class, 'registrarConsultaExterna'])->name('reception.registrar-consulta');
        Route::post('/api/triage-general', [ConsultaExternaController::class, 'procesarTriageGeneral'])->name('reception.triage-general');
        Route::get('/reception/confirmacion-registro/{id}', [ReceptionController::class, 'confirmacionRegistro'])->name('reception.confirmacion-registro');
        
        // Rutas API para emergencia
        Route::post('/api/registrar-emergencia', [EmergenciaController::class, 'registrarEmergencia'])->name('reception.registrar-emergencia');
        Route::get('/api/emergencias-activas', [EmergenciaController::class, 'getEmergenciasActivas'])->name('reception.emergencias-activas');
        Route::put('/api/emergencia/{nroEmergencia}/estado', [EmergenciaController::class, 'actualizarEstadoEmergencia'])->name('reception.actualizar-emergencia');
        
        // Rutas API para hospitalización
        Route::post('/api/registrar-hospitalizacion', [HospitalizacionController::class, 'registrarHospitalizacion'])->name('reception.registrar-hospitalizacion');
        Route::get('/api/hospitalizaciones-activas', [HospitalizacionController::class, 'getHospitalizacionesActivas'])->name('reception.hospitalizaciones-activas');
        Route::post('/api/hospitalizacion/{id}/alta', [HospitalizacionController::class, 'darAlta'])->name('reception.dar-alta');
        Route::put('/api/hospitalizacion/{id}/actualizar', [HospitalizacionController::class, 'actualizarDatos'])->name('reception.actualizar-hospitalizacion');
        
        // Rutas API para gestión de citas
        Route::get('/api/agenda-dia', [ReceptionController::class, 'getAgendaDia'])->name('reception.agenda-dia');
        Route::post('/api/nueva-cita', [ReceptionController::class, 'crearNuevaCita'])->name('reception.nueva-cita');
        Route::post('/api/cita/{id}/confirmar', [ReceptionController::class, 'confirmarCita'])->name('reception.confirmar-cita');
        Route::post('/api/cita/{id}/registrar-llegada', [ReceptionController::class, 'registrarLlegadaPaciente'])->name('reception.registrar-llegada');
        Route::post('/api/cita/{id}/cancelar', [ReceptionController::class, 'cancelarCita'])->name('reception.cancelar-cita');
        
        // Rutas API para gestión de llamadas
        Route::get('/api/llamadas-pendientes', [ReceptionController::class, 'getPendientesLlamada'])->name('reception.llamadas-pendientes');
        Route::post('/api/cita/{id}/registrar-llamada', [ReceptionController::class, 'registrarLlamadaCita'])->name('reception.registrar-llamada');
        
        // Rutas API para utilidades
        Route::get('/api/estadisticas-dashboard', [ReceptionController::class, 'getEstadisticasDashboard'])->name('reception.estadisticas');
        Route::get('/api/medicos-disponibles', [ReceptionController::class, 'buscarMedicosDisponibles'])->name('reception.medicos-disponibles');
        Route::get('/api/especialidades', [ReceptionController::class, 'getEspecialidades'])->name('reception.especialidades');

        // Rutas para flujo de pago en recepción
        Route::post('/reception/procesar-pago/{id}', [ReceptionController::class, 'procesarPago'])->name('reception.procesar-pago');
        Route::get('/reception/confirmacion/{id}', [ReceptionController::class, 'confirmacion'])->name('reception.confirmacion');
    });


    // Rutas para caja (admin y caja) - Sistema Anterior (mantener por compatibilidad)
    Route::middleware(['auth', 'role:admin|caja'])->prefix('caja')->name('caja.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CajaController::class, 'index'])->name('dashboard');
        Route::post('/procesar-pago/{id}', [\App\Http\Controllers\CajaController::class, 'procesarPago'])->name('procesar-pago');
        Route::get('/detalles/{id}', [\App\Http\Controllers\CajaController::class, 'verDetalles'])->name('detalles');
        Route::get('/reporte-diario', [\App\Http\Controllers\CajaController::class, 'reporteDiario'])->name('reporte');
        Route::get('/pacientes-registrados', [\App\Http\Controllers\CajaController::class, 'getPacientesRegistrados'])->name('pacientes-registrados');
        Route::get('/pacientes-pendientes', [\App\Http\Controllers\CajaController::class, 'getPacientesPendientes'])->name('pacientes-pendientes');
        Route::get('/servicios-disponibles', [\App\Http\Controllers\CajaController::class, 'getServiciosDisponibles'])->name('servicios-disponibles');
    });

    // Rutas médicas (admin y dirmedico)
    Route::middleware(['role:admin|dirmedico'])->group(function () {
        Route::get('/enfermeria', [NursingController::class, 'index'])->name('enfermeria.index');
        Route::get('/uti', [UtiController::class, 'index'])->name('uti.index');
        Route::get('/quirofano', [QuirofanoController::class, 'index'])->name('quirofano.index');
        Route::get('/hospitalizacion', [MedicalHospitalizacionController::class, 'index'])->name('hospitalizacion.index');
        Route::get('/consulta-externa', [DoctorController::class, 'index'])->name('consulta.index');
        
        // Test route
        Route::get('/test-doctor', function() {
            return 'DoctorController works!';
        });
        
        // Test DoctorController directly
        Route::get('/test-doctor-class', function() {
            try {
                $controller = new \App\Http\Controllers\DoctorController();
                return 'DoctorController class loaded successfully';
            } catch (\Exception $e) {
                return 'Error loading DoctorController: ' . $e->getMessage();
            }
        });
    });

    // Rutas para médicos (dirmedico)
    Route::middleware(['auth', 'role:dirmedico'])->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DoctorController::class, 'index'])->name('dashboard');
        Route::get('/consulta/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'verConsulta'])->name('ver-consulta');
        Route::post('/iniciar-consulta/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'iniciarConsulta'])->name('iniciar-consulta');
        Route::post('/completar-consulta/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'completarConsulta'])->name('completar-consulta');
    });

    // Rutas de emergencia (admin, dirmedico y emergencia)
    Route::middleware(['role:admin|dirmedico|emergencia'])->group(function () {
        Route::get('/emergencias', [EmergencyController::class, 'index'])->name('emergencias.index');
    });

    // Rutas de administración (admin y caja)
    Route::middleware(['role:admin|caja'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/caja', [\App\Http\Controllers\Admin\CajaController::class, 'index'])->name('caja.index');

        Route::get('/facturacion', function () {
            return view('admin.facturacion');
        })->name('facturacion.index');

        Route::get('/tarifarios', [\App\Http\Controllers\Admin\TarifarioController::class, 'index'])->name('tarifarios');
        Route::post('/tarifarios', [\App\Http\Controllers\Admin\TarifarioController::class, 'store'])->name('tarifarios.store');
        Route::put('/tarifarios/{tarifa}', [\App\Http\Controllers\Admin\TarifarioController::class, 'update'])->name('tarifarios.update');
        Route::delete('/tarifarios/{tarifa}', [\App\Http\Controllers\Admin\TarifarioController::class, 'destroy'])->name('tarifarios.destroy');
        
        // API routes for tarifarios
        Route::get('/api/tarifarios', [\App\Http\Controllers\Admin\TarifarioController::class, 'apiIndex'])->name('tarifarios.api.index');
        Route::get('/api/tarifarios/{tarifa}', [\App\Http\Controllers\Admin\TarifarioController::class, 'apiShow'])->name('tarifarios.api.show');

        Route::get('/seguros', [SeguroController::class, 'index'])->name('seguros');
        Route::get('/cuentas-por-cobrar', [CuentaCobrarController::class, 'index'])->name('cuentas');
    });

    // Rutas de administración (solo admin) - Incluyendo especialidades
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Rutas para especialidades
        Route::get('/especialidades', [\App\Http\Controllers\Admin\EspecialidadController::class, 'index'])->name('especialidades.index');
        Route::post('/especialidades', [\App\Http\Controllers\Admin\EspecialidadController::class, 'store'])->name('especialidades.store');
        Route::put('/especialidades/{codigo}', [\App\Http\Controllers\Admin\EspecialidadController::class, 'update'])->name('especialidades.update');
        Route::delete('/especialidades/{codigo}', [\App\Http\Controllers\Admin\EspecialidadController::class, 'destroy'])->name('especialidades.destroy');
        Route::get('/especialidades/{codigo}', [\App\Http\Controllers\Admin\EspecialidadController::class, 'show'])->name('especialidades.show');
        Route::get('/especialidades/stats', [\App\Http\Controllers\Admin\EspecialidadController::class, 'getStats'])->name('especialidades.stats');
        
        // API routes for especialidades
        Route::get('/api/especialidades', [\App\Http\Controllers\Admin\EspecialidadController::class, 'index'])->name('especialidades.api.index');
        Route::post('/api/especialidades', [\App\Http\Controllers\Admin\EspecialidadController::class, 'store'])->name('especialidades.api.store');
        Route::put('/api/especialidades/{codigo}', [\App\Http\Controllers\Admin\EspecialidadController::class, 'update'])->name('especialidades.api.update');
        Route::delete('/api/especialidades/{codigo}', [\App\Http\Controllers\Admin\EspecialidadController::class, 'destroy'])->name('especialidades.api.destroy');
        Route::get('/api/especialidades/{codigo}', [\App\Http\Controllers\Admin\EspecialidadController::class, 'show'])->name('especialidades.api.show');
        Route::get('/api/especialidades/stats', [\App\Http\Controllers\Admin\EspecialidadController::class, 'getStats'])->name('especialidades.api.stats');
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
        Route::get('/usuarios/create', [UsuariosController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{user}/edit', [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
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
