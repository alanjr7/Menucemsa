<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\QuirofanoController;
use App\Http\Controllers\QuirofanoMedicamentosController;
use App\Http\Controllers\QuirofanoManagementController;
use App\Http\Controllers\Reception\ConsultaExternaController;
use App\Http\Controllers\Reception\EmergenciaController;
use App\Http\Controllers\Reception\EmergencyIngresoController;
use App\Http\Controllers\Medical\EmergencyController;
use App\Http\Controllers\Medical\UtiController;
use App\Http\Controllers\Medical\QuirofanoController as MedicalQuirofanoController;
use App\Http\Controllers\Medical\HospitalizacionController as MedicalHospitalizacionController;
use App\Http\Controllers\Reception\HospitalizacionController as ReceptionHospitalizacionController;
use App\Http\Controllers\InternacionStaffController;
use App\Http\Controllers\InternacionMedicamentosController;
use App\Http\Controllers\HabitacionApiController;
use App\Http\Controllers\HabitacionGestionController;
use App\Http\Controllers\HabitacionAsignacionController;
use App\Http\Controllers\InternacionNurseController;
use App\Http\Controllers\Admin\SeguroController;
use App\Http\Controllers\Admin\CuentaCobrarController;
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\DoctorController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\TarifarioController;
use App\Http\Controllers\Gerencial\ReportesController;
use App\Http\Controllers\Gerencial\KpiController;
use App\Http\Controllers\Farmacia\FarmaciaDashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\EmergencyNurseController;
use App\Http\Controllers\Farmacia\PuntoVentaController;
use App\Http\Controllers\Farmacia\InventarioController;
use App\Http\Controllers\Farmacia\VentasController;
use App\Http\Controllers\Farmacia\ClientesController;
use App\Http\Controllers\Farmacia\ReporteController;
use App\Http\Controllers\Caja\CajaOperativaController;
use App\Http\Controllers\Caja\CajaGestionController;
use App\Http\Controllers\Admin\EmergencyController as AdminEmergencyController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\EmergencyStaffController;
use App\Http\Controllers\EmergencyMedicamentosController;
use App\Http\Controllers\Admin\AlmacenMedicamentosController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Medical\UtiOperativoController;
use App\Http\Controllers\UtiMedicamentosController;
use App\Http\Controllers\Admin\UtiAdminController;
use App\Http\Controllers\Reception\UtiRecepcionController;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'ip.access'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API de notificaciones del sistema (nuevo sistema persistente)
    Route::get('/api/notificaciones', [NotificationController::class, 'index'])->name('notificaciones.index');
    Route::post('/api/notificaciones/{id}/leer', [NotificationController::class, 'markAsRead'])->name('notificaciones.leer');
    Route::post('/api/notificaciones/leer-todas', [NotificationController::class, 'markAllAsRead'])->name('notificaciones.leer-todas');

    // Endpoint legacy de alertas (mantener por compatibilidad temporal)
    Route::get('/api/sistema/alertas', function() {
        $userId = auth()->id();
        $count = \App\Services\NotificationService::getUnreadCount($userId);
        $notifications = \App\Services\NotificationService::getUnreadForUser($userId, 10);

        // Transformar al formato legacy
        $alertas = array_map(fn($n) => [
            'tipo' => $n['type'],
            'nivel' => $n['color'],
            'mensaje' => $n['message'],
            'url' => $n['action_url'],
        ], $notifications);

        return response()->json(['alertas' => $alertas, 'total' => $count]);
    })->name('sistema.alertas');

    // Rutas para medicamentos de quirófano (admin, cirujano y administrador) - PRIMERO para evitar conflicto con /quirofano/{cita}
    Route::middleware(['auth', 'role:admin|cirujano|administrador'])->group(function () {
        Route::get('/quirofano/medicamentos', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'index'])->name('quirofano.medicamentos.index');
        Route::get('/quirofano/medicamentos/create', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'create'])->name('quirofano.medicamentos.create');
        Route::post('/quirofano/medicamentos', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'store'])->name('quirofano.medicamentos.store');
        Route::get('/quirofano/medicamentos/{medicamento}', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'show'])->name('quirofano.medicamentos.show');
        Route::get('/quirofano/medicamentos/{medicamento}/edit', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'edit'])->name('quirofano.medicamentos.edit');
        Route::put('/quirofano/medicamentos/{medicamento}', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'update'])->name('quirofano.medicamentos.update');
        Route::delete('/quirofano/medicamentos/{medicamento}', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'destroy'])->name('quirofano.medicamentos.destroy');
        Route::post('/quirofano/medicamentos/{medicamento}/stock', [\App\Http\Controllers\QuirofanoMedicamentosController::class, 'actualizarStock'])->name('quirofano.medicamentos.stock');
    });

    // Rutas para quirofano (acceso para admin, reception, dirmedico, cirujano y administrador)
    Route::middleware(['auth', 'role:admin|reception|dirmedico|cirujano|administrador'])->group(function () {
        // Rutas simples PRIMERO (antes que las rutas con parámetros)
        Route::get('/quirofano', [QuirofanoController::class, 'index'])->name('quirofano.index');
        Route::get('/quirofano/historial', [QuirofanoController::class, 'historial'])->name('quirofano.historial');
        Route::get('/quirofano/historial/export', [QuirofanoController::class, 'exportHistorial'])->name('quirofano.historial.export');
        Route::get('/quirofano/create', [QuirofanoController::class, 'create'])->name('quirofano.create');
        Route::get('/quirofano/calendario', [QuirofanoController::class, 'calendario'])->name('quirofano.calendario');
        
        // API routes (antes que las rutas con parámetros)
        Route::post('/quirofano/disponibilidad', [QuirofanoController::class, 'disponibilidad'])->name('quirofano.disponibilidad');
        Route::get('/quirofano/api/dashboard', [QuirofanoController::class, 'apiDashboard'])->name('quirofano.api.dashboard');
        Route::get('/quirofano/api/pacientes-cirugia', [QuirofanoController::class, 'apiPacientesCirugia'])->name('quirofano.api.pacientes-cirugia');
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
        Route::get('/quirofano/api/medicos-disponibles', [QuirofanoController::class, 'getMedicosDisponibles'])->name('quirofano.medicos-disponibles');
        Route::post('/quirofano/emergencia/{emergency_id}/iniciar', [QuirofanoController::class, 'iniciarEmergencia'])->name('quirofano.iniciar-emergencia');

        // Rutas para medicamentos durante cirugía
        Route::get('/quirofano/{cita}/medicamentos-disponibles', [QuirofanoController::class, 'getMedicamentosDisponibles'])->name('quirofano.medicamentos.disponibles')->where('cita', '[0-9]+');
        Route::get('/quirofano/{cita}/medicamentos-usados', [QuirofanoController::class, 'getMedicamentosUsados'])->name('quirofano.medicamentos.usados')->where('cita', '[0-9]+');
        Route::post('/quirofano/{cita}/medicamentos', [QuirofanoController::class, 'agregarMedicamento'])->name('quirofano.medicamentos.agregar')->where('cita', '[0-9]+');

        // Rutas para equipos médicos durante cirugía
        Route::post('/quirofano/{cita}/equipos-medicos', [QuirofanoController::class, 'agregarEquipoMedico'])->name('quirofano.equipos-medicos.agregar')->where('cita', '[0-9]+');
        Route::get('/quirofano/{cita}/equipos-medicos', [QuirofanoController::class, 'getEquiposMedicos'])->name('quirofano.equipos-medicos.lista')->where('cita', '[0-9]+');

        Route::get('/quirofano/{cita}', [QuirofanoController::class, 'show'])->name('quirofano.show')->where('cita', '[0-9]+');
        Route::get('/quirofano/{cita}/edit', [QuirofanoController::class, 'edit'])->name('quirofano.edit')->where('cita', '[0-9]+');

        // Rutas para gestión de pacientes en cirugía (rol quirófano)
        Route::get('/quirofano/pacientes-cirugia', [QuirofanoController::class, 'pacientesEnCirugia'])->name('quirofano.pacientes-cirugia');
        Route::post('/quirofano/api/emergencia/{emergencyId}/derivar-internacion', [QuirofanoController::class, 'derivarAInternacion'])->name('quirofano.derivar-internacion');
        Route::put('/quirofano/{cita}', [QuirofanoController::class, 'update'])->name('quirofano.update')->where('cita', '[0-9]+');
        Route::post('/quirofano/{cita}/iniciar', [QuirofanoController::class, 'iniciarCirugia'])->name('quirofano.iniciar')->where('cita', '[0-9]+');
        Route::post('/quirofano/{cita}/finalizar', [QuirofanoController::class, 'finalizarCirugia'])->name('quirofano.finalizar')->where('cita', '[0-9]+');
        Route::post('/quirofano/{cita}/cancelar', [QuirofanoController::class, 'cancelar'])->name('quirofano.cancelar')->where('cita', '[0-9]+');

        // Rutas para gestión de quirófanos (solo admin y cirujano)
        Route::middleware(['role:admin|cirujano'])->group(function () {
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

    // Rutas para recepción y pacientes (acceso para admin, reception, dirmedico y administrador)
    Route::middleware(['auth', 'role:admin|reception|dirmedico|administrador'])->group(function () {
        Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
        Route::get('/admision', function() {
            return redirect()->route('patients.index');
        })->name('admision.index');
        Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
        Route::get('/patients/{ci}', [\App\Http\Controllers\PatientsController::class, 'show'])->name('patients.show');

        // Rutas para Historial de Pacientes (Recepción)
        Route::get('/reception/pacientes', [\App\Http\Controllers\ReceptionController::class, 'pacientesIndex'])->name('reception.pacientes.index');
        Route::get('/reception/pacientes/{ci}', [\App\Http\Controllers\ReceptionController::class, 'pacientesHistorial'])->name('reception.pacientes.historial');
        Route::get('/reception/pacientes/{ci}/print', [\App\Http\Controllers\ReceptionController::class, 'pacientesHistorialPrint'])->name('reception.pacientes.historial.print');

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
        Route::get('/emergencia/{id}/comprobante', [EmergencyIngresoController::class, 'comprobante'])->name('reception.emergencia.comprobante');
        Route::post('/api/registrar-emergencia', [EmergenciaController::class, 'registrarEmergencia'])->name('reception.registrar-emergencia');
        Route::get('/api/emergencias-activas', [EmergenciaController::class, 'getEmergenciasActivas'])->name('reception.emergencias-activas');
        Route::put('/api/emergencia/{nroEmergencia}/estado', [EmergenciaController::class, 'actualizarEstadoEmergencia'])->name('reception.actualizar-emergencia');
        
        // Rutas API para hospitalización
        Route::post('/api/registrar-hospitalizacion', [ReceptionHospitalizacionController::class, 'registrarHospitalizacion'])->name('reception.registrar-hospitalizacion');
        Route::get('/api/hospitalizaciones-activas', [ReceptionHospitalizacionController::class, 'getHospitalizacionesActivas'])->name('reception.hospitalizaciones-activas');
        Route::post('/api/hospitalizacion/{id}/alta', [ReceptionHospitalizacionController::class, 'darAlta'])->name('reception.dar-alta');
        Route::put('/api/hospitalizacion/{id}/actualizar', [ReceptionHospitalizacionController::class, 'actualizarDatos'])->name('reception.actualizar-hospitalizacion');
        
        // Ruta para comprobante de hospitalización
        Route::get('/hospitalizacion/{id}/comprobante', [ReceptionHospitalizacionController::class, 'comprobante'])->name('reception.hospitalizacion.comprobante');
        
        // Rutas API para gestión de citas
        Route::get('/reception/agenda', function() {
            return view('reception.agenda');
        })->name('reception.agenda');
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
        
        // Rutas UTI integradas en caja operativa
        Route::get('/uti-pacientes', [CajaOperativaController::class, 'getPacientesUti'])->name('uti-pacientes');
        Route::get('/uti-detalle-cuenta/{id}', [CajaOperativaController::class, 'getDetalleCuentaUti'])->name('uti-detalle-cuenta');
        Route::post('/uti-procesar-cobro/{id}', [CajaOperativaController::class, 'procesarCobroUti'])->name('uti-procesar-cobro');
        Route::post('/uti-deposito/{id}', [CajaOperativaController::class, 'registrarDepositoUti'])->name('uti-deposito');

        // Comprobante de pago
        Route::get('/comprobante/{cuentaId}', [CajaOperativaController::class, 'comprobante'])->name('comprobante');
    });

    // Gestión de Caja - Para usuarios con rol ADMIN y ADMINISTRADOR
    Route::middleware(['auth', 'role:admin|administrador'])->prefix('caja-gestion')->name('caja.gestion.')->group(function () {
        Route::get('/', [CajaGestionController::class, 'index'])->name('index');
        Route::get('/transacciones', [CajaGestionController::class, 'getTransacciones'])->name('transacciones');
        Route::get('/transaccion/{id}', [CajaGestionController::class, 'getDetalleTransaccion'])->name('detalle-transaccion');
        Route::get('/control-cajas', [CajaGestionController::class, 'getControlCajas'])->name('control-cajas');
        Route::post('/anular-cuenta/{id}', [CajaGestionController::class, 'anularCuenta'])->name('anular');
        Route::get('/resumen-financiero', [CajaGestionController::class, 'getResumenFinanciero'])->name('resumen-financiero');
        Route::get('/auditoria', [CajaGestionController::class, 'getAuditoria'])->name('auditoria');
        Route::get('/datos-facturacion', [CajaGestionController::class, 'getDatosFacturacion'])->name('datos-facturacion');
        Route::get('/usuarios-caja', [CajaGestionController::class, 'getUsuariosCaja'])->name('usuarios-caja');
        Route::delete('/detalles/{detalleId}', [CajaGestionController::class, 'eliminarDetalle'])->name('eliminar-detalle');
        Route::get('/detalles-eliminados', [CajaGestionController::class, 'getDetallesEliminados'])->name('detalles-eliminados');
    });

    // Sistema antiguo de caja ELIMINADO - usar /caja-operativa o /caja-gestion
    // Route::middleware(['auth', 'role:admin|caja'])->prefix('caja')->name('caja.')->group(function () {
    //     Route::get('/', [\App\Http\Controllers\CajaController::class, 'index'])->name('dashboard');
    // });

    // Rutas médicas (admin, dirmedico, doctor y administrador) - SIN duplicar rutas de quirofano
    Route::middleware(['role:admin|dirmedico|doctor|administrador'])->group(function () {
        Route::get('/uti', [UtiController::class, 'index'])->name('uti.index');
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
    Route::middleware(['role:doctor|dirmedico|admin'])->group(function () {
        Route::get('/consulta-externa', [\App\Http\Controllers\DoctorController::class, 'index'])->name('consulta.index');
        Route::get('/consulta/{consultaCodigo}', [\App\Http\Controllers\DoctorController::class, 'verConsulta'])->name('consulta.ver');
        Route::post('/consulta-externa/iniciar/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'iniciarConsulta'])->name('consulta.iniciar');
        Route::post('/consulta-externa/completar/{consultaId}', [\App\Http\Controllers\DoctorController::class, 'completarConsulta'])->name('consulta.completar');
        Route::get('/api/paciente/{ci}', [\App\Http\Controllers\DoctorController::class, 'getPaciente'])->name('consulta.paciente');

        // Ruta para imprimir receta
        Route::get('/medico/receta/{receta}/print', function(\App\Models\Receta $receta) {
            $receta->load(['detalles.medicamento', 'consulta.paciente', 'consulta.especialidad', 'userMedico']);
            return view('medical.receta-print', compact('receta'));
        })->name('medico.receta.print');

        // Ruta para evolución médica en internación
        Route::get('/medico/internacion/{id}', [
            \App\Http\Controllers\Medical\HospitalizacionController::class, 'detalle'
        ])->name('medico.internacion.detalle');
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
    Route::middleware(['role:admin|caja|administrador'])->prefix('admin')->name('admin.')->group(function () {
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

        Route::get('/ingreso-precios', [\App\Http\Controllers\Admin\IngresoPrecioController::class, 'index'])->name('ingreso-precios.index');
        Route::put('/ingreso-precios', [\App\Http\Controllers\Admin\IngresoPrecioController::class, 'update'])->name('ingreso-precios.update');

        Route::get('/seguros', [SeguroController::class, 'index'])->name('seguros');
        Route::get('/seguros/historial', [SeguroController::class, 'historial'])->name('seguros.historial');
        Route::get('/seguros/historial/exportar', [SeguroController::class, 'exportarHistorial'])->name('seguros.historial.exportar');
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

    // Rutas de administración (admin y administrador) - Especialidades CRUD
    Route::middleware(['role:admin|administrador'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard principal del admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
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

    // Rutas de farmacia (admin, farmacia y administrador)
    Route::middleware(['auth', 'role:admin|farmacia|administrador'])->prefix('farmacia')->name('farmacia.')->group(function () {

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

    // Rutas gerenciales (admin, gerente y administrador)
    Route::middleware(['role:admin|gerente|administrador'])->prefix('gerencial')->name('gerencial.')->group(function () {
        // Dashboard del gerente
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
        Route::get('/reportes/data', [ReportesController::class, 'data'])->name('reportes.data');
        Route::get('/reportes/export', [ReportesController::class, 'export'])->name('reportes.export');
        Route::get('/kpis', [KpiController::class, 'index'])->name('kpis');
    });

    // Rutas de seguridad (admin, gerente, dirmedico y administrador)
    Route::middleware(['role:admin|gerente|dirmedico|administrador'])->prefix('seguridad')->name('seguridad.')->group(function () {
        Route::get('/auditoria', [App\Http\Controllers\Seguridad\AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/configuracion', [App\Http\Controllers\Seguridad\ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::get('/bitacora', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Rutas de Control de Accesos por IP
        Route::middleware(['role:admin|gerente|administrador'])->group(function () {
            Route::get('/accesos', [App\Http\Controllers\Seguridad\AccesosController::class, 'index'])->name('accesos.index');
            Route::post('/accesos', [App\Http\Controllers\Seguridad\AccesosController::class, 'store'])->name('accesos.store');
            Route::delete('/accesos/{acceso}', [App\Http\Controllers\Seguridad\AccesosController::class, 'destroy'])->name('accesos.destroy');
            Route::patch('/accesos/mode', [App\Http\Controllers\Seguridad\AccesosController::class, 'updateMode'])->name('accesos.mode');
        });
    });

    // Rutas de gestión de usuarios (admin, gerente y administrador)
    Route::middleware(['role:admin|gerente|administrador'])->prefix('user-management')->name('user-management.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Rutas de gestión de emergencias (admin y administrador - SOLO LECTURA)
    Route::middleware(['role:admin|administrador'])->prefix('admin')->name('admin.')->group(function () {
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

    // Rutas para personal de emergencias - EMERGENCIA, ENFERMERA-EMERGENCIA, ADMIN, DIR MEDICO Y ADMINISTRADOR
    Route::middleware(['role:emergencia|enfermera-emergencia|admin|dirmedico|administrador'])->prefix('emergency-staff')->name('emergency-staff.')->group(function () {
        // Rutas simples PRIMERO (antes que las rutas con parámetros)
        Route::get('/dashboard', [EmergencyStaffController::class, 'index'])->name('dashboard');
        Route::get('/create', [EmergencyStaffController::class, 'create'])->name('create');
        Route::get('/pending', [EmergencyStaffController::class, 'pending'])->name('pending');

        // API routes (antes que las rutas con parámetros)
        Route::get('/api/emergencias', [EmergencyStaffController::class, 'apiEmergencias'])->name('api.emergencias');
        Route::get('/api/estadisticas', [EmergencyStaffController::class, 'apiEstadisticas'])->name('api.estadisticas');
        Route::get('/api/medicamentos-disponibles', [EmergencyStaffController::class, 'apiMedicamentosDisponibles'])->name('api.medicamentos');

        // Rutas para gestión de medicamentos de emergencia (admin, emergencia y administrador)
        Route::middleware(['role:admin|emergencia|administrador'])->group(function () {
            Route::get('/medicamentos', [EmergencyMedicamentosController::class, 'index'])->name('medicamentos.index');
            Route::get('/medicamentos/create', [EmergencyMedicamentosController::class, 'create'])->name('medicamentos.create');
            Route::post('/medicamentos', [EmergencyMedicamentosController::class, 'store'])->name('medicamentos.store');
            Route::get('/medicamentos/{medicamento}', [EmergencyMedicamentosController::class, 'show'])->name('medicamentos.show');
            Route::get('/medicamentos/{medicamento}/edit', [EmergencyMedicamentosController::class, 'edit'])->name('medicamentos.edit');
            Route::put('/medicamentos/{medicamento}', [EmergencyMedicamentosController::class, 'update'])->name('medicamentos.update');
            Route::delete('/medicamentos/{medicamento}', [EmergencyMedicamentosController::class, 'destroy'])->name('medicamentos.destroy');
            Route::post('/medicamentos/{medicamento}/stock', [EmergencyMedicamentosController::class, 'actualizarStock'])->name('medicamentos.stock');
        });

        // Rutas para gestión de enfermeras de emergencia (admin, emergencia y administrador)
        Route::middleware(['role:admin|emergencia|administrador'])->group(function () {
            Route::get('/enfermeras', [EmergencyNurseController::class, 'index'])->name('enfermeras.index');
            Route::get('/enfermeras/create', [EmergencyNurseController::class, 'create'])->name('enfermeras.create');
            Route::post('/enfermeras', [EmergencyNurseController::class, 'store'])->name('enfermeras.store');
            Route::get('/enfermeras/{enfermera}', [EmergencyNurseController::class, 'show'])->name('enfermeras.show');
            Route::get('/enfermeras/{enfermera}/edit', [EmergencyNurseController::class, 'edit'])->name('enfermeras.edit');
            Route::put('/enfermeras/{enfermera}', [EmergencyNurseController::class, 'update'])->name('enfermeras.update');
            Route::delete('/enfermeras/{enfermera}', [EmergencyNurseController::class, 'destroy'])->name('enfermeras.destroy');
            Route::patch('/enfermeras/{enfermera}/toggle-status', [EmergencyNurseController::class, 'toggleStatus'])->name('enfermeras.toggle-status');
            Route::get('/enfermeras/{enfermera}/actividad', [EmergencyNurseController::class, 'actividad'])->name('enfermeras.actividad');

            // Rutas de gestión de permisos
            Route::get('/enfermeras/{enfermera}/permissions', [EmergencyNurseController::class, 'permissions'])->name('enfermeras.permissions');
            Route::post('/enfermeras/{enfermera}/permissions', [EmergencyNurseController::class, 'updatePermissions'])->name('enfermeras.permissions.update');

            // Rutas de auditoría
            Route::get('/auditoria', [EmergencyNurseController::class, 'auditoria'])->name('auditoria');
            Route::get('/api/auditoria', [EmergencyNurseController::class, 'apiAuditoria'])->name('api.auditoria');
        });

        // API: Obtener permisos del usuario actual (disponible para enfermera-emergencia también)
        Route::get('/api/mis-permisos', [EmergencyStaffController::class, 'apiPermisos'])->name('api.permisos');

        // Historial general de emergencias
        Route::get('/historial', [EmergencyStaffController::class, 'historialGeneral'])->name('historial.general');
        Route::get('/historial/export', [EmergencyStaffController::class, 'exportHistorialGeneral'])->name('historial.export');

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

    // Rutas para personal de internación - INTERNACION, ADMIN, DIR MEDICO, ENFERMERAS Y ADMINISTRADOR
    Route::middleware(['role:internacion|admin|dirmedico|enfermera-internacion|administrador'])->prefix('internacion-staff')->name('internacion-staff.')->group(function () {
        // Dashboard principal
        Route::get('/dashboard', [InternacionStaffController::class, 'index'])->name('dashboard');

        // Página de evaluación del paciente
        Route::get('/evaluar/{id}', [InternacionStaffController::class, 'evaluar'])->name('evaluar');

        // Página de historial del paciente
        Route::get('/historial/{id}', [InternacionStaffController::class, 'historial'])->name('historial');

        // API routes
        Route::get('/api/internaciones', [InternacionStaffController::class, 'apiInternaciones'])->name('api.internaciones');
        Route::get('/api/estadisticas', [InternacionStaffController::class, 'apiEstadisticas'])->name('api.estadisticas');
        Route::post('/api/internacion/{id}/update-status', [InternacionStaffController::class, 'updateStatus'])->name('update-status');
        Route::post('/api/internacion/{id}/derivar-uti', [InternacionStaffController::class, 'derivarAUti'])->name('derivar-uti');
        Route::post('/api/internacion/{id}/derivar-quirofano', [InternacionStaffController::class, 'derivarAQuirofano'])->name('derivar-quirofano');
        Route::post('/api/internacion/{id}/alta', [InternacionStaffController::class, 'darAlta'])->name('alta');

        // API Medicamentos para pacientes
        Route::get('/api/medicamentos-disponibles', [InternacionStaffController::class, 'apiMedicamentosDisponibles'])->name('api.medicamentos-disponibles');
        Route::get('/api/medicamentos/buscar', [InternacionStaffController::class, 'buscarMedicamentos'])->name('api.medicamentos.buscar');
        Route::get('/api/internacion/{id}/medicamentos', [InternacionStaffController::class, 'apiMedicamentos'])->name('api.medicamentos');
        Route::post('/api/internacion/{id}/medicamentos', [InternacionStaffController::class, 'storeMedicamento'])->name('api.medicamentos.store');

        // API Catering
        Route::get('/api/internacion/{id}/catering', [InternacionStaffController::class, 'apiCatering'])->name('api.catering');
        Route::post('/api/internacion/{id}/catering', [InternacionStaffController::class, 'storeCatering'])->name('api.catering.store');

        // API Precios de Catering (gestión global)
        Route::get('/api/catering-precios', [InternacionStaffController::class, 'apiCateringPrecios'])->name('api.catering-precios');
        Route::post('/api/catering-precios', [InternacionStaffController::class, 'actualizarCateringPrecios'])->name('api.catering-precios.update');

        // API Drenajes
        Route::get('/api/internacion/{id}/drenajes', [InternacionStaffController::class, 'apiDrenajes'])->name('api.drenajes');
        Route::post('/api/internacion/{id}/drenajes', [InternacionStaffController::class, 'storeDrenaje'])->name('api.drenajes.store');

        // API Equipos Médicos
        Route::get('/api/internacion/{id}/equipos-medicos', [InternacionStaffController::class, 'apiEquiposMedicos'])->name('api.equipos-medicos');

        // API Receta/Diagnóstico
        Route::post('/api/internacion/{id}/receta', [InternacionStaffController::class, 'updateReceta'])->name('api.receta.update');
        Route::post('/api/internacion/{hospitalizacion}/evolucion', [MedicalHospitalizacionController::class, 'guardarEvolucion'])->name('api.internacion.evolucion');

        // Historial General de Internaciones
        Route::get('/historial-general', [InternacionStaffController::class, 'historialGeneral'])->name('historial-general');
        Route::get('/export-historial', [InternacionStaffController::class, 'exportHistorial'])->name('export-historial');

        // Rutas para gestión de medicamentos de internación (admin, internacion y administrador)
        Route::middleware(['role:admin|internacion|administrador'])->group(function () {
            Route::get('/medicamentos', [InternacionMedicamentosController::class, 'index'])->name('medicamentos.index');
            Route::get('/medicamentos/create', [InternacionMedicamentosController::class, 'create'])->name('medicamentos.create');
            Route::post('/medicamentos', [InternacionMedicamentosController::class, 'store'])->name('medicamentos.store');
            Route::get('/medicamentos/{medicamento}', [InternacionMedicamentosController::class, 'show'])->name('medicamentos.show');
            Route::get('/medicamentos/{medicamento}/edit', [InternacionMedicamentosController::class, 'edit'])->name('medicamentos.edit');
            Route::put('/medicamentos/{medicamento}', [InternacionMedicamentosController::class, 'update'])->name('medicamentos.update');
            Route::delete('/medicamentos/{medicamento}', [InternacionMedicamentosController::class, 'destroy'])->name('medicamentos.destroy');
            Route::post('/medicamentos/{medicamento}/stock', [InternacionMedicamentosController::class, 'actualizarStock'])->name('medicamentos.stock');
        });

        // Rutas para gestión de habitaciones de internación - Vista y CRUD
        Route::get('/habitaciones', [HabitacionGestionController::class, 'index'])->name('habitaciones.index');
        Route::get('/habitaciones/create', [HabitacionGestionController::class, 'create'])->name('habitaciones.create');
        Route::post('/habitaciones', [HabitacionGestionController::class, 'store'])->name('habitaciones.store');
        Route::get('/habitaciones/{habitacion}/edit', [HabitacionGestionController::class, 'edit'])->name('habitaciones.edit');
        Route::put('/habitaciones/{habitacion}', [HabitacionGestionController::class, 'update'])->name('habitaciones.update');
        Route::delete('/habitaciones/{habitacion}', [HabitacionGestionController::class, 'destroy'])->name('habitaciones.destroy');

        // API routes para habitaciones (split-view optimizado)
        Route::get('/api/habitaciones', [HabitacionApiController::class, 'index'])->name('api.habitaciones.index');
        Route::get('/api/habitaciones/{habitacion}', [HabitacionApiController::class, 'show'])->name('api.habitaciones.show');
        Route::get('/api/pacientes-sin-habitacion', [HabitacionApiController::class, 'pacientesSinHabitacion'])->name('api.pacientes.sin-habitacion');

        // Operaciones de asignación y liberación
        Route::post('/habitaciones/{habitacion}/asignar-paciente', [HabitacionAsignacionController::class, 'asignarPaciente'])->name('habitaciones.asignar-paciente');
        Route::post('/camas/{cama}/liberar', [HabitacionAsignacionController::class, 'liberarCama'])->name('camas.liberar');

        // Rutas para gestión de enfermeras de internación (admin, internacion y administrador)
        Route::middleware(['role:admin|internacion|administrador'])->group(function () {
            Route::get('/enfermeras', [InternacionNurseController::class, 'index'])->name('enfermeras.index');
            Route::get('/enfermeras/create', [InternacionNurseController::class, 'create'])->name('enfermeras.create');
            Route::post('/enfermeras', [InternacionNurseController::class, 'store'])->name('enfermeras.store');
            Route::get('/enfermeras/{enfermera}', [InternacionNurseController::class, 'show'])->name('enfermeras.show');
            Route::get('/enfermeras/{enfermera}/edit', [InternacionNurseController::class, 'edit'])->name('enfermeras.edit');
            Route::put('/enfermeras/{enfermera}', [InternacionNurseController::class, 'update'])->name('enfermeras.update');
            Route::delete('/enfermeras/{enfermera}', [InternacionNurseController::class, 'destroy'])->name('enfermeras.destroy');
            Route::patch('/enfermeras/{enfermera}/toggle-status', [InternacionNurseController::class, 'toggleStatus'])->name('enfermeras.toggle-status');
            Route::get('/enfermeras/{enfermera}/actividad', [InternacionNurseController::class, 'actividad'])->name('enfermeras.actividad');

            // Rutas de gestión de permisos
            Route::get('/enfermeras/{enfermera}/permissions', [InternacionNurseController::class, 'permissions'])->name('enfermeras.permissions');
            Route::post('/enfermeras/{enfermera}/permissions', [InternacionNurseController::class, 'updatePermissions'])->name('enfermeras.permissions.update');
        });
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

// Ruta de diagnóstico específico para cirujano
Route::get('/test-cirujano-access', function() {
    if (!auth()->check()) {
        return 'No autenticado';
    }
    $user = auth()->user();
    $userRole = $user->role;
    $allowedRoles = ['admin', 'cirujano'];
    $hasAccess = in_array($userRole, $allowedRoles);

    return json_encode([
        'usuario' => $user->name,
        'rol_bd' => $userRole,
        'roles_permitidos' => $allowedRoles,
        'tiene_acceso' => $hasAccess,
        'isCirujano()' => $user->isCirujano()
    ], JSON_PRETTY_PRINT);
})->middleware('auth');

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
Route::middleware(['auth', 'role:admin|dirmedico|doctor|uti'])->prefix('uti-operativo')->name('uti.operativa.')->group(function () {
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

    // Rutas para gestión de medicamentos de UTI (solo admin y uti)
    Route::middleware(['role:admin|uti'])->group(function () {
        Route::get('/medicamentos', [UtiMedicamentosController::class, 'index'])->name('medicamentos.index');
        Route::get('/medicamentos/create', [UtiMedicamentosController::class, 'create'])->name('medicamentos.create');
        Route::post('/medicamentos', [UtiMedicamentosController::class, 'store'])->name('medicamentos.store');
        Route::get('/medicamentos/{medicamento}', [UtiMedicamentosController::class, 'show'])->name('medicamentos.show');
        Route::get('/medicamentos/{medicamento}/edit', [UtiMedicamentosController::class, 'edit'])->name('medicamentos.edit');
        Route::put('/medicamentos/{medicamento}', [UtiMedicamentosController::class, 'update'])->name('medicamentos.update');
        Route::delete('/medicamentos/{medicamento}', [UtiMedicamentosController::class, 'destroy'])->name('medicamentos.destroy');
        Route::post('/medicamentos/{medicamento}/stock', [UtiMedicamentosController::class, 'actualizarStock'])->name('medicamentos.stock');
    });
});

// UTI Administración - Admin y Administrador
Route::middleware(['auth', 'role:admin|administrador'])->prefix('uti-admin')->name('uti.admin.')->group(function () {
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
