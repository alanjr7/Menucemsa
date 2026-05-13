<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\QuirofanoController;
use App\Http\Controllers\QuirofanoMedicamentosController;
use App\Http\Controllers\QuirofanoManagementController;
use App\Http\Controllers\Reception\EmergencyIngresoController;
use App\Http\Controllers\Medical\EmergencyController;
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
use App\Http\Controllers\PatientsController;
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
use App\Http\Controllers\Admin\AlmacenInventarioController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\InternacionHabitacionUsoController;
use App\Http\Controllers\UtiMedicamentosController;

use Illuminate\Support\Facades\Artisan;


Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/admin/system/optimize', function () {
    try {
        Artisan::call('optimize:clear');

        Artisan::call('optimize');

        return "<h1>¡Sistema Optimizado!</h1>
                <p>Se ha refrescado el archivo .env y las rutas correctamente.</p>";
    } catch (\Exception $e) {
        return "<h1>Error al optimizar</h1>" . $e->getMessage();
    }
})->name('admin.system.optimize');

Route::middleware(['auth', 'ip.access'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('menus', MenuController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API de notificaciones del sistema (nuevo sistema persistente)
    Route::get('/api/notificaciones', [NotificationController::class, 'index'])->name('notificaciones.index');
    Route::post('/api/notificaciones/{id}/leer', [NotificationController::class, 'markAsRead'])->name('notificaciones.leer');
    Route::post('/api/notificaciones/leer-todas', [NotificationController::class, 'markAllAsRead'])->name('notificaciones.leer-todas');

    // Endpoint legacy de alertas (mantener por compatibilidad temporal)
    Route::get('/api/sistema/alertas', function () {
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
        Route::get('/quirofano/procedimientos/buscar', [QuirofanoController::class, 'buscarProcedimientos'])->name('quirofano.procedimientos.buscar');
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

        Route::put('/quirofano/{cita}', [QuirofanoController::class, 'update'])->name('quirofano.update')->where('cita', '[0-9]+');
        Route::post('/quirofano/{cita}/ejecutar', [QuirofanoController::class, 'ejecutar'])->name('quirofano.ejecutar')->where('cita', '[0-9]+');
        Route::post('/quirofano/{cita}/cancelar', [QuirofanoController::class, 'cancelar'])->name('quirofano.cancelar')->where('cita', '[0-9]+');

        // Rutas para gestión de quirófanos (solo admin y cirujano)
        Route::middleware(['role:admin|cirujano|administrador'])->group(function () {
            Route::get('/quirofanos-management', [QuirofanoManagementController::class, 'index'])->name('quirofanos.management.index');
            Route::get('/quirofanos-management/create', [QuirofanoManagementController::class, 'create'])->name('quirofanos.management.create');
            Route::post('/quirofanos-management', [QuirofanoManagementController::class, 'store'])->name('quirofanos.management.store');
            Route::get('/quirofanos-management/{quirofano}', [QuirofanoManagementController::class, 'show'])->name('quirofanos.management.show');
            Route::get('/quirofanos-management/{quirofano}/edit', [QuirofanoManagementController::class, 'edit'])->name('quirofanos.management.edit');
            Route::put('/quirofanos-management/{quirofano}', [QuirofanoManagementController::class, 'update'])->name('quirofanos.management.update');
            Route::delete('/quirofanos-management/{quirofano}', [QuirofanoManagementController::class, 'destroy'])->name('quirofanos.management.destroy');
            Route::post('/quirofanos-management/{quirofano}/estado', [QuirofanoManagementController::class, 'cambiarEstado'])->name('quirofanos.management.estado');

            // Ruta para ver detalles de cirugía finalizada (solo lectura)
            Route::get('/quirofano/{cita}/detalles', [QuirofanoController::class, 'showDetails'])->name('quirofano.show-details')->where('cita', '[0-9]+');
            // API para obtener siguiente número de quirófano
            Route::get('/api/quirofanos/next-number', [QuirofanoManagementController::class, 'getNextNumber'])->name('quirofanos.api.next-number');
        });
    });

    // Rutas para recepción (acceso para admin, reception, dirmedico y administrador)
    Route::middleware(['auth', 'role:admin|reception|dirmedico|administrador'])->group(function () {
        Route::get('/reception', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('reception');
        Route::get('/admision', function () {
            return redirect()->route('patients.index');
        })->name('admision.index');
    });

    // Rutas para pacientes (acceso para todos los roles)
    Route::middleware(['auth'])->group(function () {
        Route::get('/patients', [\App\Http\Controllers\PatientsController::class, 'index'])->name('patients.index');
        Route::get('/patients/{ci}', [\App\Http\Controllers\PatientsController::class, 'show'])->name('patients.show');
        Route::get('/patients/{ci}/print', [\App\Http\Controllers\PatientsController::class, 'print'])->name('patients.print');

        // Dar de Alta (roles autorizados)
        Route::middleware(['role:admin|administrador|cirujano|emergencia|internacion'])->group(function () {
            Route::get('/patients-dar-de-alta', [\App\Http\Controllers\PatientsController::class, 'darDeAltaIndex'])->name('patients.dar-de-alta.index');
            Route::post('/patients/{ci}/dar-de-alta', [\App\Http\Controllers\PatientsController::class, 'darDeAlta'])->name('patients.dar-de-alta');
        });

        // Historial de Altas (solo admin y administrador)
        Route::middleware(['role:admin|administrador'])->group(function () {
            Route::get('/patients-historial-altas', [\App\Http\Controllers\PatientsController::class, 'historialAltas'])->name('patients.historial-altas');
        });

        // Ruta para listado de pacientes en recepción (área clínica excepto caja)
        Route::middleware(['role:admin|reception|dirmedico|emergencia|uti|internacion|cirujano|doctor|enfermera-emergencia|enfermera-internacion|administrador|farmacia|gerente'])->group(function () {
            Route::get('/reception/pacientes', [\App\Http\Controllers\ReceptionController::class, 'pacientesIndex'])->name('reception.pacientes.index');

            Route::get('/reception/confirmacion-registro/{id}', [ReceptionController::class, 'confirmacionRegistro'])->name('reception.confirmacion-registro');

            // Comprobantes (usados por IngresoGeneralController tras procesar ingreso)
            Route::get('/emergencia/{id}/comprobante', [EmergencyIngresoController::class, 'comprobante'])->name('reception.emergencia.comprobante');
            Route::get('/hospitalizacion/{id}/comprobante', [ReceptionHospitalizacionController::class, 'comprobante'])->name('reception.hospitalizacion.comprobante');

            // Alta y actualización de hospitalización (operaciones post-ingreso)
            Route::post('/api/hospitalizacion/{id}/alta', [ReceptionHospitalizacionController::class, 'darAlta'])->name('reception.dar-alta');
            Route::put('/api/hospitalizacion/{id}/actualizar', [ReceptionHospitalizacionController::class, 'actualizarDatos'])->name('reception.actualizar-hospitalizacion');

            // Rutas para formulario unificado de ingreso general
            Route::get('/reception/ingreso-general', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'index'])->name('reception.ingreso-general');
            Route::get('/reception/ingreso-general/buscar-paciente', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'buscarPaciente'])->name('reception.ingreso-general.buscar-paciente');
            Route::get('/reception/ingreso-general/buscar-garante', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'buscarGarante'])->name('reception.ingreso-general.buscar-garante');
            Route::post('/reception/ingreso-general/procesar', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'procesarIngreso'])->name('reception.ingreso-general.procesar');
            Route::get('/reception/ingreso-general/especialidades', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'buscarEspecialidades'])->name('reception.ingreso-general.especialidades');
            Route::post('/reception/ingreso-general/especialidades', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'crearEspecialidad'])->name('reception.ingreso-general.crear-especialidad');
            Route::get('/reception/ingreso-general/medicos', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'buscarMedicos'])->name('reception.ingreso-general.medicos');
            Route::post('/reception/ingreso-general/medicos', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'crearMedico'])->name('reception.ingreso-general.crear-medico');
            Route::get('/reception/ingreso-general/especialidades-lista', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'getEspecialidadesLista'])->name('reception.ingreso-general.especialidades-lista');
            Route::get('/reception/ingreso-general/medicos-por-especialidad/{codigo}', [\App\Http\Controllers\Reception\IngresoGeneralController::class, 'getMedicosPorEspecialidad'])->name('reception.ingreso-general.medicos-por-especialidad');

            // Rutas API para gestión de citas
            Route::get('/reception/agenda', function () {
                return view('reception.agenda');
            })->name('reception.agenda');
            Route::get('/reception/citas/{id}/edit', [ReceptionController::class, 'editCita'])->name('reception.citas.edit');
            Route::put('/reception/citas/{id}', [ReceptionController::class, 'updateCita'])->name('reception.citas.update');
            Route::get('/api/agenda-dia', [ReceptionController::class, 'getAgendaDia'])->name('reception.agenda-dia');
            Route::post('/api/nueva-cita', [ReceptionController::class, 'crearNuevaCita'])->name('reception.nueva-cita');
            Route::post('/api/cita/{id}/confirmar', [ReceptionController::class, 'confirmarCita'])->name('reception.confirmar-cita');
            Route::post('/api/cita/{id}/registrar-llegada', [ReceptionController::class, 'registrarLlegadaPaciente'])->name('reception.registrar-llegada');
            Route::post('/api/cita/{id}/cancelar', [ReceptionController::class, 'cancelarCita'])->name('reception.cancelar-cita');
            Route::delete('/api/cita/{id}', [ReceptionController::class, 'eliminarCita'])->name('reception.eliminar-cita');
            Route::post('/api/cita/{id}/restaurar', [ReceptionController::class, 'restaurarCita'])->name('reception.restaurar-cita');
            Route::get('/api/citas-eliminadas', [ReceptionController::class, 'getCitasEliminadas'])->name('reception.citas-eliminadas');
            Route::post('/api/cita/{id}/asistida', [ReceptionController::class, 'marcarAsistida'])->name('reception.marcar-asistida');
            Route::post('/api/cita/{id}/no-asistida', [ReceptionController::class, 'marcarNoAsistida'])->name('reception.marcar-no-asistida');
            Route::get('/api/agenda-semanal', [ReceptionController::class, 'getAgendaSemanal'])->name('reception.agenda-semanal');
            Route::get('/api/citas/paciente/{ci}', [ReceptionController::class, 'getCitasPorPaciente'])->name('reception.citas-paciente');

            // Rutas API para gestión de llamadas
            Route::get('/api/llamadas-pendientes', [ReceptionController::class, 'getPendientesLlamada'])->name('reception.llamadas-pendientes');
            Route::post('/api/cita/{id}/registrar-llamada', [ReceptionController::class, 'registrarLlamadaCita'])->name('reception.registrar-llamada');

            // Rutas API para utilidades
            Route::get('/api/estadisticas-dashboard', [ReceptionController::class, 'getEstadisticasDashboard'])->name('reception.estadisticas');
            Route::get('/api/medicos-disponibles', [ReceptionController::class, 'buscarMedicosDisponibles'])->name('reception.medicos-disponibles');
            Route::get('/api/especialidades', [ReceptionController::class, 'getEspecialidades'])->name('reception.especialidades');

            // Rutas API para garantes
            Route::get('/api/buscar-garante', [ReceptionController::class, 'buscarGarante'])->name('reception.buscar-garante');
            Route::post('/api/buscar-garante-exacto', [ReceptionController::class, 'buscarGaranteExacto'])->name('reception.buscar-garante-exacto');
            Route::post('/api/registrar-garante', [ReceptionController::class, 'registrarGarante'])->name('reception.registrar-garante');
            Route::post('/api/registrar-paciente-cita', [ReceptionController::class, 'registrarPacienteParaCita'])->name('reception.registrar-paciente-cita');

            // Rutas para completar datos de paciente temporal
            Route::get('/reception/completar-datos-paciente/{emergencyId}', [EmergencyIngresoController::class, 'mostrarFormularioCompletarDatos'])->name('reception.completar-datos-paciente');
            Route::post('/reception/completar-datos-paciente', [EmergencyIngresoController::class, 'completarDatosPacienteTemporal'])->name('reception.completar-datos-paciente.store');

            // Rutas para flujo de pago en recepción
            Route::post('/reception/procesar-pago/{id}', [ReceptionController::class, 'procesarPago'])->name('reception.procesar-pago');
            Route::get('/reception/confirmacion/{id}', [ReceptionController::class, 'confirmacion'])->name('reception.confirmacion');
        });
    });


    // NUEVAS RUTAS DE CAJA - Sistema Integrado (2026)
    // Caja Operativa - Para usuarios con rol CAJA
    Route::middleware(['auth', 'role:admin|caja|administrador'])->prefix('caja-operativa')->name('caja.operativa.')->group(function () {
        Route::get('/', [CajaOperativaController::class, 'index'])->name('index');
        Route::post('/abrir', [CajaOperativaController::class, 'abrirCaja'])->name('abrir');
        Route::post('/cerrar', [CajaOperativaController::class, 'cerrarCaja'])->name('cerrar');
        Route::get('/pacientes-pendientes', [CajaOperativaController::class, 'getPacientesPendientes'])->name('pacientes-pendientes');
        Route::get('/detalle-cuenta/{id}', [CajaOperativaController::class, 'getDetalleCuenta'])->name('detalle-cuenta');
        Route::post('/procesar-cobro', [CajaOperativaController::class, 'procesarCobro'])->name('procesar-cobro');
        Route::get('/resumen-dia', [CajaOperativaController::class, 'getResumenDia'])->name('resumen-dia');
        Route::get('/buscar-paciente', [CajaOperativaController::class, 'buscarPaciente'])->name('buscar-paciente');
        Route::get('/tarifas', [CajaOperativaController::class, 'getTarifas'])->name('tarifas');

        // Comprobante de pago
        Route::get('/comprobante/{cuentaId}', [CajaOperativaController::class, 'comprobante'])->name('comprobante');
    });

    // Gestión de Caja - Para usuarios con rol ADMIN y ADMINISTRADOR
    Route::middleware(['auth', 'role:admin|administrador'])->prefix('caja-gestion')->name('caja.gestion.')->group(function () {
        Route::get('/exportar-auditoria', [CajaGestionController::class, 'exportarAuditoria'])->name('exportar.auditoria');
        Route::get('/exportar-cajas', [CajaGestionController::class, 'exportarCajas'])->name('exportar.cajas');
        Route::get('/exportar-transacciones', [CajaGestionController::class, 'exportarTransacciones'])->name('exportar.transacciones');
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
        Route::get('/medico/receta/{receta}/print', function (\App\Models\Receta $receta) {
            $receta->load(['detalles.medicamento', 'consulta.paciente', 'consulta.especialidad', 'userMedico']);
            return view('medical.receta-print', compact('receta'));
        })->name('medico.receta.print');

        // Ruta para evolución médica en internación
        Route::get('/medico/internacion/{id}', [
            \App\Http\Controllers\Medical\HospitalizacionController::class,
            'detalle'
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
        Route::post('/seguros', [SeguroController::class, 'store'])->name('seguros.store');
        Route::put('/seguros/{seguro}', [SeguroController::class, 'update'])->name('seguros.update');
        Route::delete('/seguros/{seguro}', [SeguroController::class, 'destroy'])->name('seguros.destroy');
        Route::post('/seguros/{seguro}/estado', [SeguroController::class, 'cambiarEstado']);
        Route::get('/seguros/historial/excel', [SeguroController::class, 'exportarExcel'])
            ->name('seguros.historial.excel');

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

        // Gestión de pacientes
        Route::get('/pacientes/gestionar', [\App\Http\Controllers\PatientsController::class, 'gestionar'])->name('pacientes.gestionar');

        // Episodios
        Route::get('/episodios', [\App\Http\Controllers\Admin\EpisodioController::class, 'index'])->name('episodios.index');
        Route::get('/episodios/paciente/{ci}', [\App\Http\Controllers\Admin\EpisodioController::class, 'porPaciente'])->name('episodios.paciente');
        Route::get('/episodios/{id}', [\App\Http\Controllers\Admin\EpisodioController::class, 'show'])->name('episodios.show');
        Route::get('/episodios/{id}/excel', [\App\Http\Controllers\Admin\EpisodioController::class, 'exportExcel'])->name('episodios.excel');
        Route::get('/episodios/{id}/pdf', [\App\Http\Controllers\Admin\EpisodioController::class, 'exportPdf'])->name('episodios.pdf');

        // Edición y gestión de pacientes
        Route::get('/pacientes/{ci}/edit', [\App\Http\Controllers\PatientsController::class, 'edit'])->name('patients.edit');
        Route::put('/pacientes/{ci}', [\App\Http\Controllers\PatientsController::class, 'update'])->name('patients.update');
        Route::get('/pacientes/{ci}/cuenta', [\App\Http\Controllers\PatientsController::class, 'verCuenta'])->name('cuentas.show');
        Route::delete('/cuentas/{cuentaId}/detalles/{detalleId}', [\App\Http\Controllers\PatientsController::class, 'eliminarItemCuenta'])->name('cuentas.eliminar-item');

        Route::get('especialidades', [EspecialidadController::class, 'index'])->name('especialidades.index');
        Route::get('especialidades/create', [EspecialidadController::class, 'create'])->name('especialidades.create');
        Route::post('especialidades', [EspecialidadController::class, 'store'])->name('especialidades.store');
        Route::get('especialidades/{especialidad}/edit', [EspecialidadController::class, 'edit'])->name('especialidades.edit');
        Route::put('especialidades/{especialidad}', [EspecialidadController::class, 'update'])->name('especialidades.update');
        Route::delete('especialidades/{especialidad}', [EspecialidadController::class, 'destroy'])->name('especialidades.destroy');

        // Procedimientos clínicos
        Route::resource('procedimientos', \App\Http\Controllers\Admin\ProcedimientosController::class);

        // Rutas para gestión de doctores
        Route::get('doctors', [DoctorController::class, 'index'])->name('doctors.index');
        Route::get('doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
        Route::post('doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::get('doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');

        // Rutas API para doctores
        Route::get('api/medicos-por-especialidad', [DoctorController::class, 'getMedicosByEspecialidad'])->name('doctors.by-especialidad');

        // Rutas para gestión de cirujanos
        Route::get('cirujanos', [\App\Http\Controllers\Admin\CirujanoController::class, 'index'])->name('cirujanos.index');
        Route::get('cirujanos/create', [\App\Http\Controllers\Admin\CirujanoController::class, 'create'])->name('cirujanos.create');
        Route::post('cirujanos', [\App\Http\Controllers\Admin\CirujanoController::class, 'store'])->name('cirujanos.store');
        Route::get('cirujanos/{cirujano}/edit', [\App\Http\Controllers\Admin\CirujanoController::class, 'edit'])->name('cirujanos.edit');
        Route::put('cirujanos/{cirujano}', [\App\Http\Controllers\Admin\CirujanoController::class, 'update'])->name('cirujanos.update');
        Route::delete('cirujanos/{cirujano}', [\App\Http\Controllers\Admin\CirujanoController::class, 'destroy'])->name('cirujanos.destroy');

        // Camillas (UTI y Emergencia)
        Route::resource('camillas', \App\Http\Controllers\Admin\CamillaController::class);
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
        Route::post('/{user}/temporal-password', [UserManagementController::class, 'generateTemporalPassword'])->name('temporal-password');
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

        // Rutas de transferencia masiva (ANTES de las rutas con wildcard {id})
        Route::get('/almacen-medicamentos/transferir', [AlmacenMedicamentosController::class, 'transferirForm'])->name('almacen-medicamentos.transferir.form');
        Route::post('/almacen-medicamentos/transferir', [AlmacenMedicamentosController::class, 'procesarTransferencia'])->name('almacen-medicamentos.transferir.procesar');

        // Agregar stock masivo al almacén central
        Route::get('/almacen-medicamentos/agregar-stock', [AlmacenMedicamentosController::class, 'agregarStockForm'])->name('almacen-medicamentos.agregar-stock.form');
        Route::post('/almacen-medicamentos/agregar-stock', [AlmacenMedicamentosController::class, 'procesarAgregarStock'])->name('almacen-medicamentos.agregar-stock.procesar');

        // Rutas de historial de dispensaciones (ANTES de las rutas con wildcard {id})
        Route::get('/almacen-medicamentos/historial/dispensaciones', [AlmacenMedicamentosController::class, 'historialDispensaciones'])->name('almacen-medicamentos.historial');
        Route::get('/almacen-medicamentos/historial/dispensaciones/exportar', [AlmacenMedicamentosController::class, 'exportarHistorial'])->name('almacen-medicamentos.historial.exportar');

        Route::get('/almacen-medicamentos/{almacenMedicamento}', [AlmacenMedicamentosController::class, 'show'])->name('almacen-medicamentos.show');
        Route::get('/almacen-medicamentos/{almacenMedicamento}/edit', [AlmacenMedicamentosController::class, 'edit'])->name('almacen-medicamentos.edit');
        Route::put('/almacen-medicamentos/{almacenMedicamento}', [AlmacenMedicamentosController::class, 'update'])->name('almacen-medicamentos.update');
        Route::delete('/almacen-medicamentos/{almacenMedicamento}', [AlmacenMedicamentosController::class, 'destroy'])->name('almacen-medicamentos.destroy');
        Route::post('/almacen-medicamentos/{almacenMedicamento}/actualizar-stock', [AlmacenMedicamentosController::class, 'actualizarStock'])->name('almacen-medicamentos.actualizar-stock');
        Route::post('/almacen-medicamentos/{almacenMedicamento}/dispensar', [AlmacenMedicamentosController::class, 'dispensar'])->name('almacen-medicamentos.dispensar');
        Route::get('/almacen-medicamentos/{almacenMedicamento}/historial', [AlmacenMedicamentosController::class, 'historialItem'])->name('almacen-medicamentos.historial-item');
        Route::get('/almacen-medicamentos/{almacenMedicamento}/pacientes-area', [AlmacenMedicamentosController::class, 'pacientesPorArea'])->name('almacen-medicamentos.pacientes-area');
        Route::get('/almacen-medicamentos/reporte/bajo-stock', [AlmacenMedicamentosController::class, 'reporteBajoStock'])->name('almacen-medicamentos.reporte.bajo-stock');
        Route::get('/almacen-medicamentos/reporte/bajo-stock/exportar', [AlmacenMedicamentosController::class, 'exportarBajoStock'])->name('almacen-medicamentos.reporte.bajo-stock.exportar');
        Route::get('/almacen-medicamentos/reporte/vencimiento', [AlmacenMedicamentosController::class, 'reporteVencimiento'])->name('almacen-medicamentos.reporte.vencimiento');
        Route::get('/almacen-medicamentos/reporte/vencimiento/exportar', [AlmacenMedicamentosController::class, 'exportarVencimiento'])->name('almacen-medicamentos.reporte.vencimiento.exportar');
        Route::get('/almacen-medicamentos/area/{area}', [AlmacenMedicamentosController::class, 'porArea'])->name('almacen-medicamentos.por-area');

        // Almacén Inventario (activos locales, sin llaves foráneas)
        Route::resource('almacen-inventario', AlmacenInventarioController::class);
    });

    // Detalle y registro de paciente en dispensación — accesible por admin y personal de área
    Route::middleware(['role:admin|administrador|emergencia|enfermera-emergencia|cirujano|internacion|enfermera-internacion|uti|doctor|farmacia|dirmedico'])
        ->prefix('admin')->name('admin.')->group(function () {
            Route::get('/almacen-medicamentos/historial/dispensaciones/{dispensacion}', [AlmacenMedicamentosController::class, 'detalleDispensacion'])->name('almacen-medicamentos.detalle-dispensacion');
            Route::post('/almacen-medicamentos/historial/dispensaciones/{dispensacion}/registrar-paciente', [AlmacenMedicamentosController::class, 'registrarPaciente'])->name('almacen-medicamentos.registrar-paciente');
        });

    // API routes accesibles por recepción y emergencia (fuera del middleware de emergencia)
    Route::middleware(['auth'])->prefix('api')->group(function () {
        Route::get('/emergencias-temporales', [EmergencyStaffController::class, 'apiEmergenciasTemporales']);
        Route::get('/buscar-paciente', [AlmacenMedicamentosController::class, 'buscarPacienteApi']);
    });

    // Rutas para personal de emergencias - EMERGENCIA, ENFERMERA-EMERGENCIA, ADMIN, DIR MEDICO Y ADMINISTRADOR
    Route::middleware(['role:emergencia|enfermera-emergencia|admin|dirmedico|administrador|uti'])->prefix('emergency-staff')->name('emergency-staff.')->group(function () {
        // Rutas simples PRIMERO (antes que las rutas con parámetros)
        Route::get('/dashboard', [EmergencyStaffController::class, 'index'])->name('dashboard');
        Route::get('/create', [EmergencyStaffController::class, 'create'])->name('create');
        // API routes (antes que las rutas con parámetros)
        Route::get('/api/emergencias', [EmergencyStaffController::class, 'apiEmergencias'])->name('api.emergencias');
        Route::get('/api/estadisticas', [EmergencyStaffController::class, 'apiEstadisticas'])->name('api.estadisticas');
        Route::get('/api/medicamentos-disponibles', [EmergencyStaffController::class, 'apiMedicamentosDisponibles'])->name('api.medicamentos');

        // Camillas de emergencia
        Route::get('/camillas', [\App\Http\Controllers\EmergencyStaff\CamillaUsoController::class, 'index'])->name('camillas.index');
        Route::post('/camillas', [\App\Http\Controllers\EmergencyStaff\CamillaUsoController::class, 'store'])->name('camillas.store');

        // Rutas para gestión de medicamentos de emergencia (admin, emergencia, enfermera-emergencia y administrador)
        Route::middleware(['role:admin|emergencia|enfermera-emergencia|administrador'])->group(function () {
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

        // Rutas de Catering Masivo
        Route::get('/catering', [InternacionStaffController::class, 'cateringIndex'])->name('catering.index');
        Route::post('/catering/registrar', [InternacionStaffController::class, 'cateringRegistrar'])->name('catering.registrar');

        // Gestión de Precios de Catering (Admin)
        Route::get('/catering/gestion', [InternacionStaffController::class, 'gestionCatering'])->name('catering.gestion');

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

        // Rutas para gestión de medicamentos de internación (admin, internacion, administrador y enfermera-internacion)
        Route::middleware(['role:admin|internacion|administrador|enfermera-internacion'])->group(function () {
            Route::get('/medicamentos', [InternacionMedicamentosController::class, 'index'])->name('medicamentos.index');
            Route::get('/medicamentos/create', [InternacionMedicamentosController::class, 'create'])->name('medicamentos.create');
            Route::post('/medicamentos', [InternacionMedicamentosController::class, 'store'])->name('medicamentos.store');
            Route::get('/medicamentos/{medicamento}', [InternacionMedicamentosController::class, 'show'])->name('medicamentos.show');
            Route::get('/medicamentos/{medicamento}/edit', [InternacionMedicamentosController::class, 'edit'])->name('medicamentos.edit');
            Route::put('/medicamentos/{medicamento}', [InternacionMedicamentosController::class, 'update'])->name('medicamentos.update');
            Route::delete('/medicamentos/{medicamento}', [InternacionMedicamentosController::class, 'destroy'])->name('medicamentos.destroy');
            Route::post('/medicamentos/{medicamento}/stock', [InternacionMedicamentosController::class, 'actualizarStock'])->name('medicamentos.stock');
        });

        // Registrar uso de habitación/cama (cargo a cuenta del paciente)
        Route::get('/habitaciones/registro-uso', [InternacionHabitacionUsoController::class, 'index'])->name('habitaciones.registro-uso');
        Route::post('/habitaciones/registro-uso', [InternacionHabitacionUsoController::class, 'store'])->name('habitaciones.registro-uso.store');

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
Route::get('/test-emergency-access', function () {
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
Route::get('/test-role-middleware', function () {
    return 'Middleware role:emergencia funcionó correctamente';
})->middleware(['auth', 'role:emergencia']);

// Ruta de diagnóstico específico para cirujano
Route::get('/test-cirujano-access', function () {
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


// Inventario de medicamentos UTI (solo lectura)
Route::middleware(['auth', 'role:admin|uti|administrador|dirmedico|doctor'])->get('/uti/medicamentos', [UtiMedicamentosController::class, 'index'])->name('uti.operativa.medicamentos.readonly');

// Dashboard UTI - Terapia Intensiva
Route::middleware(['auth', 'role:uti|admin|dirmedico|administrador'])->get('/uti/dashboard', [\App\Http\Controllers\UtiController::class, 'dashboard'])->name('uti.dashboard');

// Rutas de evaluación clínica de pacientes
Route::middleware(['auth', 'role:emergencia|enfermera-emergencia|uti|internacion|enfermera-internacion|cirujano|admin|administrador|dirmedico|reception'])->group(function () {
    Route::get('/evaluacion/{ci}/historial', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'historial'])->name('evaluacion.historial');
    Route::get('/evaluacion/{ci}/print/{evaluacion}', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'print'])->name('evaluacion.print');
    Route::delete('/evaluacion/{ci}/historial/{evaluacion}', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'destroy'])->name('evaluacion.destroy')->middleware('role:admin|administrador');
});

Route::middleware(['auth', 'role:emergencia|enfermera-emergencia|uti|internacion|enfermera-internacion|cirujano'])->group(function () {
    Route::get('/evaluacion/emergencia/{ci}', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'show'])->name('evaluacion.emergencia');
    Route::get('/evaluacion/uti/{ci}', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'show'])->name('evaluacion.uti');
    Route::get('/evaluacion/internacion/{ci}', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'show'])->name('evaluacion.internacion');
    Route::post('/evaluacion/{ci}/store', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'store'])->name('evaluacion.store');
});

// AJAX endpoints de evaluacion — todos los roles con formulario de evaluación
Route::middleware(['auth', 'role:emergencia|enfermera-emergencia|uti|internacion|enfermera-internacion|cirujano|neonato|admin|administrador|dirmedico'])->group(function () {
    Route::get('/api/evaluacion/medicamentos', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'buscarMedicamentos']);
    Route::get('/api/evaluacion/insumos', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'buscarInsumos']);
    Route::get('/api/evaluacion/procedimientos', [\App\Http\Controllers\Reception\EvaluacionPacienteController::class, 'buscarProcedimientos']);
});

// =============================================================================
// NEONATO — Admin (Gestionar Clínica)
// =============================================================================
Route::middleware(['auth', 'role:admin|administrador'])
    ->prefix('admin/neonato')
    ->name('admin.neonato.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/medicamentos', [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'medicamentos'])->name('medicamentos');

        // Cunas CRUD
        Route::get('/cunas',                [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'cunas'])->name('cunas');
        Route::get('/cunas/create',         [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'createCuna'])->name('cunas.create');
        Route::post('/cunas',               [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'storeCuna'])->name('cunas.store');
        Route::get('/cunas/{cuna}/edit',    [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'editCuna'])->name('cunas.edit');
        Route::put('/cunas/{cuna}',         [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'updateCuna'])->name('cunas.update');
        Route::delete('/cunas/{cuna}',      [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'destroyCuna'])->name('cunas.destroy');

        // Recién nacidos (solo lectura)
        Route::get('/recien-nacidos',           [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'recienNacidos'])->name('recien-nacidos');
        Route::get('/recien-nacidos/{neonato}', [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'showRecienNacido'])->name('recien-nacidos.show');

        // Procedimientos (solo lectura)
        Route::get('/procedimientos', [\App\Http\Controllers\Admin\NeonatoAdminController::class, 'procedimientos'])->name('procedimientos');
    });

// =============================================================================
// NEONATO — Rol operativo
// =============================================================================
Route::middleware(['auth', 'role:neonato|admin|administrador|dirmedico'])
    ->prefix('neonato')
    ->name('neonato.')
    ->group(function () {
        // Cunas (ruta estática primero)
        Route::get('/cunas',  [\App\Http\Controllers\Neonato\NeonatoController::class, 'cunas'])->name('cunas');
        Route::post('/cunas', [\App\Http\Controllers\Neonato\NeonatoController::class, 'storeCunaUso'])->name('cunas.store');

        // AJAX búsqueda madre
        Route::get('/api/buscar-madre', [\App\Http\Controllers\Neonato\NeonatoController::class, 'buscarMadre'])->name('api.buscar-madre');

        // Recién nacidos
        Route::get('/',               [\App\Http\Controllers\Neonato\NeonatoController::class, 'index'])->name('index');
        Route::get('/add',            [\App\Http\Controllers\Neonato\NeonatoController::class, 'create'])->name('create');
        Route::post('/add',           [\App\Http\Controllers\Neonato\NeonatoController::class, 'store'])->name('store');
        Route::get('/medicamentos',        [\App\Http\Controllers\Neonato\NeonatoController::class, 'medicamentos'])->name('medicamentos');
        Route::get('/procedimientos',     [\App\Http\Controllers\Neonato\NeonatoController::class, 'procedimientos'])->name('procedimientos');
        Route::get('/{neonato}/datos',    [\App\Http\Controllers\Neonato\NeonatoController::class, 'show'])->name('show');
        Route::patch('/{neonato}/status', [\App\Http\Controllers\Neonato\NeonatoController::class, 'updateStatus'])->name('status');
        Route::get('/{neonato}/historial',[\App\Http\Controllers\Neonato\NeonatoController::class, 'historial'])->name('historial');
        Route::get('/{neonato}/evaluar',  [\App\Http\Controllers\Neonato\NeonatoController::class, 'evaluar'])->name('evaluar');
        Route::post('/{neonato}/evaluar',                  [\App\Http\Controllers\Neonato\NeonatoController::class, 'storeEvaluacion'])->name('evaluar.store');
        Route::delete('/{neonato}/evaluar/{evaluacion}',   [\App\Http\Controllers\Neonato\NeonatoController::class, 'destroyEvaluacion'])->name('evaluar.destroy');
    });

require __DIR__ . '/auth.php';
