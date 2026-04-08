<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\QuirofanoController;
use App\Http\Controllers\QuirofanoManagementController;
use App\Http\Controllers\Reception\ConsultaExternaController;
use App\Http\Controllers\Reception\EmergenciaController;
use App\Http\Controllers\Reception\EmergencyIngresoController;
use App\Http\Controllers\Medical\EmergencyController;
use App\Http\Controllers\Medical\NursingController;
use App\Http\Controllers\Medical\UtiController;
use App\Http\Controllers\Medical\QuirofanoController as MedicalQuirofanoController;
use App\Http\Controllers\Medical\HospitalizacionController as MedicalHospitalizacionController;
use App\Http\Controllers\Reception\HospitalizacionController as ReceptionHospitalizacionController;
use App\Http\Controllers\Admin\SeguroController;
use App\Http\Controllers\Admin\CuentaCobrarController;
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\DoctorController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\TarifarioController;
use App\Http\Controllers\Gerencial\ReportesController;
use App\Http\Controllers\Gerencial\KpiController;
use App\Http\Controllers\Farmacia\FarmaciaDashboardController;
use App\Http\Controllers\Seguridad\UsuariosController;

use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Farmacia\PuntoVentaController;
use App\Http\Controllers\Farmacia\InventarioController;
use App\Http\Controllers\Farmacia\VentasController;
use App\Http\Controllers\Farmacia\ClientesController;
use App\Http\Controllers\Farmacia\ReporteController;
use App\Http\Controllers\Caja\CajaOperativaController;
use App\Http\Controllers\Caja\CajaGestionController;
use App\Http\Controllers\Admin\EmergencyController as AdminEmergencyController;
use App\Http\Controllers\EmergencyStaffController;
use App\Http\Controllers\Admin\AlmacenMedicamentosController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Medical\UtiOperativoController;
use App\Http\Controllers\Admin\UtiAdminController;
use App\Http\Controllers\Caja\UtiCajaController;
use App\Http\Controllers\Reception\UtiRecepcionController;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para quirofano (acceso para admin, reception y dirmedico)
    Route::middleware(['auth', 'role:admin|reception|dirmedico'])->group(function () {
        // Rutas simples PRIMERO (antes que las rutas con parámetros)
        Route::get('/quirofano', [QuirofanoController::class, 'index'])->name('quirofano.index');
        Route::get('/quirofano/historial', [QuirofanoController::class, 'historial'])->name('quirofano.historial');
        Route::get('/quirofano/create', [QuirofanoController::class, 'create'])->name('quirofano.create');
        Route::get('/quirofano/calendario', [QuirofanoController::class, 'calendario'])->name('quirofano.calendario');
        
        // API routes (antes que las rutas con parámetros)
        Route::post('/quirofano/disponibilidad', [QuirofanoController::class, 'disponibilidad'])->name('quirofano.disponibilidad');
        Route::get('/api/quirofanos-disponibles', [QuirofanoController::class, 'getQuirofanosDisponibles'])->name('api.quirofanos-disponibles');
        Route::get('/api/paciente/{ci}', [QuirofanoController::class, 'getPaciente'])->name('api.paciente');
        Route::get('/api/medico/{ci}', [QuirofanoController::class, 'getMedico'])->name('api.medico');
        Route::get('/api/pacientes-lista', [QuirofanoController::class, 'getListaPacientes'])->name('api.pacientes-lista');
        Route::get('/api/medicos-lista', [QuirofanoController::class, 'getListaMedicos'])->name('api.medicos-lista');
        
        // Rutas con parámetros {cita} al FINAL
        Route::post('/quirofano', [QuirofanoController::class, 'store'])->name('quirofano.store');

        // Rutas para programar cirugías desde emergencias
        Route::get('/quirofano/emergencia/{emergency_id}/programar', [QuirofanoController::class, 'programarEmergencia'])->name('quirofano.programar-emergencia');
        Route::post('/quirofano/emergencia/store', [QuirofanoController::class, 'storeEmergencia'])->name('quirofano.store-emergencia');
        Route::post('/quirofano/emergencia/{emergency_id}/iniciar', [QuirofanoController::class, 'iniciarEmergencia'])->name('quirofano.iniciar-emergencia');

        Route::get('/quirofano/{cita}', [QuirofanoController::class, 'show'])->name('quirofano.show');
        Route::get('/quirofano/{cita}/edit', [QuirofanoController::class, 'edit'])->name('quirofano.edit');
        Route::put('/quirofano/{cita}', [QuirofanoController::class, 'update'])->name('quirofano.update');
        Route::post('/quirofano/{cita}/iniciar', [QuirofanoController::class, 'iniciarCirugia'])->name('quirofano.iniciar');
        Route::post('/quirofano/{cita}/finalizar', [QuirofanoController::class, 'finalizarCirugia'])->name('quirofano.finalizar');
        Route::post('/quirofano/{cita}/cancelar', [QuirofanoController::class, 'cancelar'])->name('quirofano.cancelar');

        // Rutas para gestión de quirófanos (solo admin)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/quirofanos-management', [QuirofanoManagementController::class, 'index'])->name('quirofanos.management.index');
            Route::get('/quirofanos-management/create', [QuirofanoManagementController::class, 'create'])->name('quirofanos.management.create');
            Route::post('/quirofanos-management', [QuirofanoManagementController::class, 'store'])->name('quirofanos.management.store');
            Route::get('/quirofanos-management/{quirofano}', [QuirofanoManagementController::class, 'show'])->name('quirofanos.management.show');
            Route::get('/quirofanos-management/{quirofano}/edit', [QuirofanoManagementController::class, 'edit'])->name('quirofanos.management.edit');
            Route::put('/quirofanos-management/{quirofano}', [QuirofanoManagementController::class, 'update'])->name('quirofanos.management.update');
            Route::delete('/quirofanos-management/{quirofano}', [QuirofanoManagementController::class, 'destroy'])->name('quirofanos.management.destroy');
            Route::post('/quirofanos-management/{quirofano}/estado', [QuirofanoManagementController::class, 'cambiarEstado'])->name('quirofanos.management.estado');
            
            // API para obtener siguiente número de quirófano
            Route::get('/api/quirofanos/next-number', [QuirofanoManagementController::class, 'getNextNumber'])->name('quirofanos.api.next-number');
        });
    });

    // Rutas para recepción y pacientes (acceso para admin, reception y dirmedico)
    Route::middleware(['auth', 'role:admin|reception|dirmedico'])->group(function () {
        Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
        Route::get('/admision', [\App\Http\Controllers\ReceptionController::class, 'admision'])->name('admision.index');
        Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
        Route::get('/patients/{ci}', [\App\Http\Controllers\PatientsController::class, 'show'])->name('patients.show');
        
        // Rutas para las nuevas páginas separadas
        Route::get('/reception/consulta-externa', [ConsultaExternaController::class, 'index'])->name('reception.consulta-externa');
        Route::get('/reception/emergencia', [EmergenciaController::class, 'index'])->name('reception.emergencia');
        Route::get('/reception/hospitalizacion', [ReceptionHospitalizacionController::class, 'index'])->name('reception.hospitalizacion');
        
        // Rutas API para consulta externa
        Route::post('/api/buscar-paciente', [ConsultaExternaController::class, 'buscarPaciente'])->name('reception.buscar-paciente');
        Route::post('/api/registrar-consulta-externa', [ConsultaExternaController::class, 'registrarConsultaExterna'])->name('reception.registrar-consulta');
        Route::post('/api/triage-general', [ConsultaExternaController::class, 'procesarTriageGeneral'])->name('reception.triage-general');
        Route::get('/reception/confirmacion-registro/{id}', [ReceptionController::class, 'confirmacionRegistro'])->name('reception.confirmacion-registro');
        
        // Rutas API para emergencia - Nuevo flujo
        Route::post('/api/emergency-ingreso', [EmergencyIngresoController::class, 'registrarIngreso'])->name('reception.emergency-ingreso');
        Route::get('/api/emergency-activas', [EmergencyIngresoController::class, 'getEmergenciasActivas'])->name('reception.emergency-activas');
        Route::post('/api/registrar-emergencia', [EmergenciaController::class, 'registrarEmergencia'])->name('reception.registrar-emergencia');
        Route::get('/api/emergencias-activas', [EmergenciaController::class, 'getEmergenciasActivas'])->name('reception.emergencias-activas');
        Route::put('/api/emergencia/{nroEmergencia}/estado', [EmergenciaController::class, 'actualizarEstadoEmergencia'])->name('reception.actualizar-emergencia');
        
        // Rutas API para hospitalización
        Route::post('/api/registrar-hospitalizacion', [ReceptionHospitalizacionController::class, 'registrarHospitalizacion'])->name('reception.registrar-hospitalizacion');
        Route::get('/api/hospitalizaciones-activas', [ReceptionHospitalizacionController::class, 'getHospitalizacionesActivas'])->name('reception.hospitalizaciones-activas');
        Route::post('/api/hospitalizacion/{id}/alta', [ReceptionHospitalizacionController::class, 'darAlta'])->name('reception.dar-alta');
        Route::put('/api/hospitalizacion/{id}/actualizar', [ReceptionHospitalizacionController::class, 'actualizarDatos'])->name('reception.actualizar-hospitalizacion');
        
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

        // Rutas para completar datos de paciente temporal
        Route::get('/reception/completar-datos-paciente/{emergencyId}', [EmergencyIngresoController::class, 'mostrarFormularioCompletarDatos'])->name('reception.completar-datos-paciente');
        Route::post('/reception/completar-datos-paciente', [EmergencyIngresoController::class, 'completarDatosPacienteTemporal'])->name('reception.completar-datos-paciente.store');
        
        // Rutas para flujo de pago en recepción
        Route::post('/reception/procesar-pago/{id}', [ReceptionController::class, 'procesarPago'])->name('reception.procesar-pago');
        Route::get('/reception/confirmacion/{id}', [ReceptionController::class, 'confirmacion'])->name('reception.confirmacion');
    });


    // NUEVAS RUTAS DE CAJA - Sistema Integrado (2026)
    // Caja Operativa - Para usuarios con rol CAJA
    Route::middleware(['auth', 'role:admin|caja'])->prefix('caja-operativa')->name('caja.operativa.')->group(function () {
        Route::get('/', [CajaOperativaController::class, 'index'])->name('index');
        Route::post('/abrir', [CajaOperativaController::class, 'abrirCaja'])->name('abrir');
        Route::post('/cerrar', [CajaOperativaController::class, 'cerrarCaja'])->name('cerrar');
        Route::get('/pacientes-pendientes', [CajaOperativaController::class, 'getPacientesPendientes'])->name('pacientes-pendientes');
        Route::get('/detalle-cuenta/{id}', [CajaOperativaController::class, 'getDetalleCuenta'])->name('detalle-cuenta');
        Route::post('/procesar-cobro', [CajaOperativaController::class, 'procesarCobro'])->name('procesar-cobro');
        Route::get('/resumen-dia', [CajaOperativaController::class, 'getResumenDia'])->name('resumen-dia');
        Route::get('/buscar-paciente', [CajaOperativaController::class, 'buscarPaciente'])->name('buscar-paciente');
        Route::get('/tarifas', [CajaOperativaController::class, 'getTarifas'])->name('tarifas');
    });

    // Gestión de Caja - Para usuarios con rol ADMIN
    Route::middleware(['auth', 'role:admin'])->prefix('caja-gestion')->name('caja.gestion.')->group(function () {
        Route::get('/', [CajaGestionController::class, 'index'])->name('index');
        Route::get('/transacciones', [CajaGestionController::class, 'getTransacciones'])->name('transacciones');
        Route::get('/transaccion/{id}', [CajaGestionController::class, 'getDetalleTransaccion'])->name('detalle-transaccion');
        Route::get('/control-cajas', [CajaGestionController::class, 'getControlCajas'])->name('control-cajas');
        Route::get('/resumen-financiero', [CajaGestionController::class, 'getResumenFinanciero'])->name('resumen-financiero');
        Route::get('/auditoria', [CajaGestionController::class, 'getAuditoria'])->name('auditoria');
        Route::get('/datos-facturacion', [CajaGestionController::class, 'getDatosFacturacion'])->name('datos-facturacion');
        Route::get('/usuarios-caja', [CajaGestionController::class, 'getUsuariosCaja'])->name('usuarios-caja');
    });

    // Sistema antiguo de caja ELIMINADO - usar /caja-operativa o /caja-gestion
    // Route::middleware(['auth', 'role:admin|caja'])->prefix('caja')->name('caja.')->group(function () {
    //     Route::get('/', [\App\Http\Controllers\CajaController::class, 'index'])->name('dashboard');
    // });

    // Rutas médicas (admin, dirmedico y doctor) - SIN duplicar rutas de quirofano
    Route::middleware(['role:admin|dirmedico|doctor'])->group(function () {
        Route::get('/enfermeria', [NursingController::class, 'index'])->name('enfermeria.index');
        Route::get('/uti', [UtiController::class, 'index'])->name('uti.index');
        Route::get('/hospitalizacion', [MedicalHospitalizacionController::class, 'index'])->name('hospitalizacion.index');
        // Rutas de control administrativo para consulta externa (solo admin)
        Route::get('/consulta-externa/historial/{ci_medico?}', [\App\Http\Controllers\DoctorController::class, 'verHistorialMedico'])->name('consulta.historial-medico');
        Route::get('/consulta-externa/pacientes/{ci_medico?}', [\App\Http\Controllers\DoctorController::class, 'verPacientesMedico'])->name('consulta.pacientes-medicos');
        
        // Rutas administrativas para gestión de consulta externa
        Route::get('/admin/consulta-externa-gestion', [\App\Http\Controllers\DoctorController::class, 'vistaControlTotal'])->name('admin.consulta-externa-gestion');
        
        // Vista del médico para atender pacientes
        Route::get('/medico/dashboard', [\App\Http\Controllers\Medical\DoctorDashboardController::class, 'index'])->name('medico.dashboard');
        Route::post('/medico/atender-paciente', [\App\Http\Controllers\Medical\DoctorDashboardController::class, 'atenderPaciente'])->name('medico.atender-paciente');
        
        // Test route
        // Route::get('/test-doctor', function() {
        //     return 'DoctorController works!';
        // });
        
        // Test DoctorController directly
        // Route::get('/test-doctor-class', function() {
        //     try {
        //         $controller = new \App\Http\Controllers\DoctorController();
        //         return 'DoctorController class loaded successfully';
        //     } catch (\Exception $e) {
        //         return 'Error loading DoctorController: ' . $e->getMessage();
        //     }
        // });
    });

    // Rutas exclusivas para doctores (vista personal de consulta externa)
    Route::middleware(['role:doctor|dirmedico'])->group(function () {
        Route::get('/consulta-externa', [\App\Http\Controllers\DoctorController::class, 'index'])->name('consulta.index');
        Route::get('/consulta/{consultaCodigo}', [\App\Http\Controllers\DoctorController::class, 'verConsulta'])->name('consulta.ver');
        Route::post('/consulta-externa/iniciar/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'iniciarConsulta'])->name('consulta.iniciar');
        Route::post('/consulta-externa/completar/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'completarConsulta'])->name('consulta.completar');
        Route::get('/api/paciente/{ci}', [\App\Http\Controllers\DoctorController::class, 'getPaciente'])->name('consulta.paciente');
    });

    // Rutas para médicos (dirmedico)
    Route::middleware(['auth', 'role:dirmedico'])->prefix('doctor')->name('doctor.')->group(function () {
        // Route::get('/', [\App\Http\Controllers\DoctorController::class, 'index'])->name('dashboard');
        // Route::get('/consulta/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'verConsulta'])->name('ver-consulta');
        // Route::post('/iniciar-consulta/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'iniciarConsulta'])->name('iniciar-consulta');
        // Route::post('/completar-consulta/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'completarConsulta'])->name('completar-consulta');
    });

    // Rutas de emergencia (admin, dirmedico y emergencia)
    Route::middleware(['role:admin|dirmedico|emergencia'])->group(function () {
        Route::get('/emergencias', [EmergencyController::class, 'index'])->name('emergencias.index');
    });

    // Rutas de administración (admin y caja) - SIN caja antigua (usar nuevo sistema)
    Route::middleware(['role:admin|caja'])->prefix('admin')->name('admin.')->group(function () {
        // Caja Central antiguo ELIMINADO - usar /caja-operativa o /caja-gestion
        
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
        Route::post('/seguros', [SeguroController::class, 'store'])->name('seguros.store');
        Route::put('/seguros/{seguro}', [SeguroController::class, 'update'])->name('seguros.update');
        Route::delete('/seguros/{seguro}', [SeguroController::class, 'destroy'])->name('seguros.destroy');
        
        // API routes para seguros
        Route::get('/api/seguros', [SeguroController::class, 'apiIndex'])->name('seguros.api.index');
        Route::get('/api/seguros/{seguro}', [SeguroController::class, 'show'])->name('seguros.api.show');
        Route::get('/api/preautorizaciones', [SeguroController::class, 'getPreautorizaciones'])->name('seguros.api.preautorizaciones');
        Route::post('/api/preautorizaciones/{cuentaId}/estado', [SeguroController::class, 'cambiarEstadoPreautorizacion'])->name('seguros.api.cambiar-estado');

        Route::get('/cuentas-por-cobrar', [CuentaCobrarController::class, 'index'])->name('cuentas');
        
        // API routes para cuentas por cobrar
        Route::get('/api/cuentas', [CuentaCobrarController::class, 'apiIndex'])->name('cuentas.api.index');
        Route::get('/api/cuentas/{id}', [CuentaCobrarController::class, 'show'])->name('cuentas.api.show');
        Route::post('/api/cuentas/{id}/pago', [CuentaCobrarController::class, 'registrarPago'])->name('cuentas.api.pago');
        Route::get('/api/cuentas-emergencias', [CuentaCobrarController::class, 'getCuentasEmergencias'])->name('cuentas.api.emergencias');
        Route::get('/api/reporte-morosidad', [CuentaCobrarController::class, 'getReporteMorosidad'])->name('cuentas.api.morosidad');
    });

    // Rutas de administración (solo admin) - Especialidades CRUD
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard principal del admin
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::get('especialidades', [EspecialidadController::class, 'index'])->name('especialidades.index');
        Route::get('especialidades/create', [EspecialidadController::class, 'create'])->name('especialidades.create');
        Route::post('especialidades', [EspecialidadController::class, 'store'])->name('especialidades.store');
        Route::get('especialidades/{especialidad}/edit', [EspecialidadController::class, 'edit'])->name('especialidades.edit');
        Route::put('especialidades/{especialidad}', [EspecialidadController::class, 'update'])->name('especialidades.update');
        Route::delete('especialidades/{especialidad}', [EspecialidadController::class, 'destroy'])->name('especialidades.destroy');

        // Rutas para gestión de doctores
        Route::get('doctors', [DoctorController::class, 'index'])->name('doctors.index');
        Route::get('doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
        Route::post('doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::get('doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');

        // Rutas API para doctores
        Route::get('api/medicos-por-especialidad', [DoctorController::class, 'getMedicosByEspecialidad'])->name('doctors.by-especialidad');
    });

    // Rutas de farmacia (admin y farmacia)
    Route::middleware(['auth', 'role:admin|farmacia'])->prefix('farmacia')->name('farmacia.')->group(function () {

        // URL: /farmacia -> Llama a FarmaciaDashboardController
        Route::get('/', [FarmaciaDashboardController::class, 'index'])->name('index');

        // URL: /farmacia/punto-de-venta -> Llama a PuntoVentaController
        Route::get('/punto-de-venta', [PuntoVentaController::class, 'index'])->name('pos');
        Route::post('/punto-de-venta/procesar', [PuntoVentaController::class, 'procesarVenta'])->name('pos.procesar');

        // URL: /farmacia/inventario -> Llama a InventarioController
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
        Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
        Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');

        Route::get('/ventas', [VentasController::class, 'index'])->name('ventas');
        Route::get('/ventas/{codigoVenta}', [VentasController::class, 'show'])->name('ventas.show');
        Route::delete('/ventas/{codigoVenta}', [VentasController::class, 'destroy'])->name('ventas.destroy');

        Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes');
        Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');
        Route::put('/clientes/{id}', [ClientesController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{id}', [ClientesController::class, 'destroy'])->name('clientes.destroy');

        Route::get('/reporte', [ReporteController::class, 'index'])->name('reporte');
        Route::post('/reporte/filtrar', [ReporteController::class, 'filtrar'])->name('reporte.filtrar');
    });

    // Rutas gerenciales (admin y gerente)
    Route::middleware(['role:admin|gerente'])->prefix('gerencial')->name('gerencial.')->group(function () {
        // Dashboard del gerente
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
        Route::get('/kpis', [KpiController::class, 'index'])->name('kpis');
    });

    // Rutas de seguridad (admin, gerente y dirmedico)
    Route::middleware(['role:admin|gerente|dirmedico'])->prefix('seguridad')->name('seguridad.')->group(function () {
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

    // Rutas de gestión de emergencias (admin - SOLO LECTURA)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/emergencies', [AdminEmergencyController::class, 'index'])->name('emergencies.index');
        Route::get('/emergencies/{emergency}', [AdminEmergencyController::class, 'show'])->name('emergencies.show');
        
        // API routes para admin (solo lectura)
        Route::get('/api/emergencias', [AdminEmergencyController::class, 'apiIndex'])->name('emergencies.api.index');
        Route::get('/api/emergencias/{emergency}', [AdminEmergencyController::class, 'apiShow'])->name('emergencies.api.show');
        
        // Rutas para almacén de medicamentos
        Route::get('/almacen-medicamentos', [AlmacenMedicamentosController::class, 'index'])->name('almacen-medicamentos.index');
        Route::get('/almacen-medicamentos/create', [AlmacenMedicamentosController::class, 'create'])->name('almacen-medicamentos.create');
        Route::post('/almacen-medicamentos', [AlmacenMedicamentosController::class, 'store'])->name('almacen-medicamentos.store');
        Route::get('/almacen-medicamentos/{almacenMedicamento}', [AlmacenMedicamentosController::class, 'show'])->name('almacen-medicamentos.show');
        Route::get('/almacen-medicamentos/{almacenMedicamento}/edit', [AlmacenMedicamentosController::class, 'edit'])->name('almacen-medicamentos.edit');
        Route::put('/almacen-medicamentos/{almacenMedicamento}', [AlmacenMedicamentosController::class, 'update'])->name('almacen-medicamentos.update');
        Route::delete('/almacen-medicamentos/{almacenMedicamento}', [AlmacenMedicamentosController::class, 'destroy'])->name('almacen-medicamentos.destroy');
        Route::post('/almacen-medicamentos/{almacenMedicamento}/actualizar-stock', [AlmacenMedicamentosController::class, 'actualizarStock'])->name('almacen-medicamentos.actualizar-stock');
        Route::get('/almacen-medicamentos/reporte/bajo-stock', [AlmacenMedicamentosController::class, 'reporteBajoStock'])->name('almacen-medicamentos.reporte.bajo-stock');
        Route::get('/almacen-medicamentos/reporte/vencimiento', [AlmacenMedicamentosController::class, 'reporteVencimiento'])->name('almacen-medicamentos.reporte.vencimiento');
        Route::get('/almacen-medicamentos/area/{area}', [AlmacenMedicamentosController::class, 'porArea'])->name('almacen-medicamentos.por-area');
    });

    // API routes accesibles por recepción y emergencia (fuera del middleware de emergencia)
    Route::middleware(['auth'])->prefix('api')->group(function () {
        Route::get('/emergencias-temporales', [EmergencyStaffController::class, 'apiEmergenciasTemporales']);
        Route::post('/completar-datos-paciente-temporal', [EmergencyIngresoController::class, 'completarDatosPacienteTemporal']);
    });

    // Rutas para personal de emergencias - EMERGENCIA, ADMIN Y DIR MEDICO
    Route::middleware(['role:emergencia|admin|dirmedico'])->prefix('emergency-staff')->name('emergency-staff.')->group(function () {
        // Rutas simples PRIMERO (antes que las rutas con parámetros)
        Route::get('/dashboard', [EmergencyStaffController::class, 'index'])->name('dashboard');
        Route::get('/create', [EmergencyStaffController::class, 'create'])->name('create');
        Route::get('/pending', [EmergencyStaffController::class, 'pending'])->name('pending');

        // API routes (antes que las rutas con parámetros)
        Route::get('/api/emergencias', [EmergencyStaffController::class, 'apiEmergencias'])->name('api.emergencias');
        Route::get('/api/estadisticas', [EmergencyStaffController::class, 'apiEstadisticas'])->name('api.estadisticas');
        Route::get('/api/medicamentos-disponibles', [EmergencyStaffController::class, 'apiMedicamentosDisponibles'])->name('api.medicamentos');

        // Rutas con parámetros {emergency} al FINAL
        Route::get('/{emergency}/evaluacion', [EmergencyStaffController::class, 'evaluacion'])->name('evaluacion');
        Route::post('/{emergency}/guardar-evaluacion', [EmergencyStaffController::class, 'guardarEvaluacion'])->name('guardar-evaluacion');
        Route::get('/{emergency}/historial', [EmergencyStaffController::class, 'historial'])->name('historial');
        Route::post('/{emergency}/update-status', [EmergencyStaffController::class, 'updateStatus'])->name('update-status');
        Route::post('/{emergency}/derivar', [EmergencyStaffController::class, 'derivar'])->name('derivar');
        Route::post('/{emergency}/alta', [EmergencyStaffController::class, 'darAlta'])->name('alta');
        Route::get('/{emergency}', [EmergencyStaffController::class, 'show'])->name('show');
        Route::get('/{emergency}/edit', [EmergencyStaffController::class, 'edit'])->name('edit');
    });
});

// Ruta de diagnóstico específico para emergency-staff
Route::get('/test-emergency-access', function() {
    if (!auth()->check()) {
        return 'No autenticado';
    }
    $user = auth()->user();
    $userRole = $user->role;
    $allowedRoles = ['emergencia'];
    $hasAccess = in_array($userRole, $allowedRoles);
    
    return json_encode([
        'usuario' => $user->name,
        'rol_bd' => $userRole,
        'roles_permitidos' => $allowedRoles,
        'tiene_acceso' => $hasAccess,
        'isEmergencia()' => $user->isEmergencia()
    ], JSON_PRETTY_PRINT);
})->middleware('auth');

// Ruta de prueba CON middleware role:emergencia (igual que emergency-staff)
Route::get('/test-role-middleware', function() {
    return 'Middleware role:emergencia funcionó correctamente';
})->middleware(['auth', 'role:emergencia']);

// Ruta de prueba para farmacia
Route::get('/test-farmacia', function() {
    if (!auth()->check()) {
        return 'No autenticado';
    }
    
    $user = auth()->user();
    return 'Usuario: ' . $user->name . ', Rol: ' . $user->role . ', isFarmacia: ' . ($user->isFarmacia() ? 'true' : 'false');
})->middleware('auth');

// Rutas UTI - Módulo de Terapia Intensiva
// =================================================================================================

// UTI Operativo - Vista clínica (admin, dirmedico, doctor, enfermeria, uti)
Route::middleware(['auth', 'role:admin|dirmedico|doctor|enfermeria|uti'])->prefix('uti-operativo')->name('uti.operativa.')->group(function () {
    Route::get('/', [UtiOperativoController::class, 'index'])->name('index');
    Route::get('/paciente/{id}', [UtiOperativoController::class, 'show'])->name('paciente.show');
    
    // API routes
    Route::get('/api/pacientes', [UtiOperativoController::class, 'getPacientesUti']);
    Route::get('/api/paciente/{id}/detalle', [UtiOperativoController::class, 'getPacienteDetalle']);
    Route::post('/api/paciente/{id}/signos', [UtiOperativoController::class, 'guardarSignosVitales']);
    Route::post('/api/paciente/{id}/evolucion', [UtiOperativoController::class, 'guardarEvolucion']);
    Route::post('/api/paciente/{id}/validar-dia', [UtiOperativoController::class, 'validarDia']);
    Route::post('/api/paciente/{id}/medicamento', [UtiOperativoController::class, 'registrarMedicamento']);
    Route::post('/api/paciente/{id}/insumo', [UtiOperativoController::class, 'registrarInsumo']);
    Route::post('/api/paciente/{id}/alimentacion', [UtiOperativoController::class, 'registrarAlimentacion']);
    Route::put('/api/paciente/{id}/estado-clinico', [UtiOperativoController::class, 'cambiarEstadoClinico']);
    Route::post('/api/paciente/{id}/alta-clinica', [UtiOperativoController::class, 'darAltaClinica']);
    Route::post('/api/paciente/{id}/trasladar', [UtiOperativoController::class, 'trasladarPaciente']);
    Route::get('/api/camas-disponibles', [UtiOperativoController::class, 'getCamasDisponibles']);
    Route::post('/api/paciente/{id}/asignar-cama', [UtiOperativoController::class, 'asignarCama']);
    Route::get('/api/medicamentos', [UtiOperativoController::class, 'getMedicamentosDisponibles']);
    Route::get('/api/insumos', [UtiOperativoController::class, 'getInsumosDisponibles']);
});

// UTI Administración - Solo admin
Route::middleware(['auth', 'role:admin'])->prefix('uti-admin')->name('uti.admin.')->group(function () {
    Route::get('/', [UtiAdminController::class, 'index'])->name('index');
    Route::get('/camas', [UtiAdminController::class, 'camas'])->name('camas');
    Route::get('/control-financiero', [UtiAdminController::class, 'controlFinanciero'])->name('control-financiero');
    Route::get('/tarifario', [UtiAdminController::class, 'tarifario'])->name('tarifario');
    
    // API routes
    Route::get('/api/estadisticas', [UtiAdminController::class, 'getEstadisticas']);
    Route::get('/api/camas-grid', [UtiAdminController::class, 'getCamasGrid']);
    Route::get('/api/pacientes', [UtiAdminController::class, 'getPacientes']);
    Route::get('/api/costos/{admissionId}', [UtiAdminController::class, 'getCostosPaciente']);
    Route::post('/api/camas', [UtiAdminController::class, 'crearCama']);
    Route::put('/api/camas/{id}', [UtiAdminController::class, 'actualizarCama']);
    Route::post('/api/camas/{id}/estado', [UtiAdminController::class, 'cambiarEstadoCama']);
    Route::get('/api/tarifario', [UtiAdminController::class, 'getTarifario']);
    Route::post('/api/tarifario', [UtiAdminController::class, 'crearTarifa']);
    Route::put('/api/tarifario/{id}', [UtiAdminController::class, 'actualizarTarifa']);
    Route::get('/api/alertas', [UtiAdminController::class, 'getAlertas']);
    Route::post('/api/preautorizacion/{admissionId}', [UtiAdminController::class, 'actualizarPreautorizacion']);
});

// UTI Caja - Admin y caja
Route::middleware(['auth', 'role:admin|caja'])->prefix('uti-caja')->name('uti.caja.')->group(function () {
    Route::get('/', [UtiCajaController::class, 'index'])->name('index');
    Route::get('/api/pacientes', [UtiCajaController::class, 'getPacientesUti']);
    Route::get('/api/detalle-cuenta/{id}', [UtiCajaController::class, 'getDetalleCuenta']);
    Route::post('/api/procesar-cobro/{id}', [UtiCajaController::class, 'procesarCobro']);
    Route::post('/api/deposito/{id}', [UtiCajaController::class, 'registrarDeposito']);
});

// UTI Recepción - Admin, reception, dirmedico
Route::middleware(['auth', 'role:admin|reception|dirmedico'])->prefix('reception/uti')->name('reception.uti.')->group(function () {
    Route::get('/ingreso', [UtiRecepcionController::class, 'index'])->name('ingreso');
    
    // API routes
    Route::post('/api/buscar-paciente', [UtiRecepcionController::class, 'buscarPaciente']);
    Route::post('/api/registrar-ingreso', [UtiRecepcionController::class, 'registrarIngreso']);
    Route::get('/api/camas-disponibles', [UtiRecepcionController::class, 'getCamasDisponibles']);
    Route::get('/api/emergencias-pendientes', [UtiRecepcionController::class, 'getEmergenciasPendientes']);
    Route::get('/api/seguros', [UtiRecepcionController::class, 'getSeguros']);
});

require __DIR__.'/auth.php';
