<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar revisión de llaves foráneas para poder truncar la tabla
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Menu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Dashboard (General)
        Menu::create([
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'active_pattern' => 'dashboard',
            'icon_path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'color' => 'blue',
            'roles' => null, // Todos
            'order' => 10,
        ]);

        // 2. Recepción
        Menu::create([
            'name' => 'Recepción',
            'route' => 'reception',
            'active_pattern' => 'reception',
            'icon_path' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
            'color' => 'blue',
            'roles' => 'reception',
            'order' => 20,
        ]);

        // 2.5 Pacientes Registrados (Recepción)
        $pacientesRegistrados = Menu::create([
            'name' => 'Pacientes Registrados',
            'active_pattern' => 'reception.pacientes*',
            'icon_path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'color' => 'blue',
            'roles' => 'reception',
            'order' => 22,
        ]);

        $pacientesRegistrados->children()->create([
            'name' => 'Buscar Pacientes',
            'route' => 'reception.pacientes.index',
            'roles' => 'reception',
            'order' => 1,
        ]);

        // 3. Panel Enfermería Emergencia (Con Submenús)
        $panelEnfermeria = Menu::create([
            'name' => 'Panel Enfermería Emergencia',
            'active_pattern' => 'emergency-staff.*',
            'icon_path' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'color' => 'purple',
            'roles' => 'enfermera-emergencia',
            'order' => 25,
        ]);

        $panelEnfermeria->children()->createMany([
            ['name' => 'Panel Principal', 'route' => 'emergency-staff.dashboard', 'roles' => 'enfermera-emergencia', 'order' => 1],
            ['name' => 'Camillas', 'route' => 'emergency-staff.camillas.index', 'roles' => 'enfermera-emergencia', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'emergency-staff.medicamentos.index', 'roles' => 'enfermera-emergencia', 'order' => 2],
        ]);

        // 3.5 Panel Enfermería Internación (Con Submenús)
        $panelEnfermeriaInt = Menu::create([
            'name' => 'Panel Enfermería Internación',
            'active_pattern' => 'internacion-staff.*',
            'icon_path' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'color' => 'indigo',
            'roles' => 'enfermera-internacion',
            'order' => 27,
        ]);

        $panelEnfermeriaInt->children()->createMany([
          ['name' => 'Panel Principal', 'route' => 'internacion-staff.dashboard', 'roles' => 'enfermera-internacion', 'order' => 1],
            ['name' => 'Registrar Habitación', 'route' => 'internacion-staff.habitaciones.registro-uso', 'roles' => 'enfermera-internacion', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'internacion-staff.medicamentos.index', 'roles' => 'enfermera-internacion', 'order' => 3],
            ['name' => 'Catering', 'route' => 'internacion-staff.catering.index', 'roles' => 'enfermera-internacion', 'order' => 5],
            ]);

        // 3.7 Panel UTI - Terapia Intensiva (Con Submenús)
        $panelUti = Menu::create([
            'name' => 'UTI - Terapia Intensiva',
            'active_pattern' => 'emergency-staff.*,uti.*',
            'icon_path' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'color' => 'cyan',
            'roles' => 'uti',
            'order' => 28,
        ]);

        $panelUti->children()->createMany([
            ['name' => 'Panel', 'route' => 'uti.dashboard', 'roles' => 'uti', 'order' => 1],
            ['name' => 'Camillas', 'route' => 'emergency-staff.camillas.index', 'roles' => 'uti', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'uti.operativa.medicamentos.readonly', 'roles' => 'uti', 'order' => 3],
        ]);

        // 4. Emergencias (Operativo - rol emergencia)
        $emergencias = Menu::create([
            'name' => 'Emergencias',
            'active_pattern' => 'emergency-staff.*',
            'icon_path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'color' => 'red',
            'roles' => 'emergencia',
            'order' => 30,
        ]);

        $emergencias->children()->createMany([
            ['name' => 'Panel Principal', 'route' => 'emergency-staff.dashboard', 'roles' => 'emergencia', 'order' => 1],
            ['name' => 'Medicamentos', 'route' => 'emergency-staff.medicamentos.index', 'roles' => 'emergencia', 'order' => 2],
            ['name' => 'Enfermeras', 'route' => 'emergency-staff.enfermeras.index', 'roles' => 'emergencia', 'order' => 4],
            ['name' => 'Camillas', 'route' => 'emergency-staff.camillas.index', 'roles' => 'emergencia', 'order' => 5],
        ]);

        // 5. Pacientes (Con Submenús) - Todos los roles
        $pacientes = Menu::create([
            'name' => 'Pacientes',
            'active_pattern' => 'patients*,consulta*,uti*,quirofano*,admin/emergencies*',
            'icon_path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'color' => 'blue',
            'roles' => null, // Todos los roles
            'order' => 20,
        ]);

        $pacientes->children()->createMany([
            ['name' => 'Maestro de Pacientes', 'route' => 'patients.index', 'roles' => null, 'order' => 1],
            ['name' => 'Gestionar Pacientes', 'route' => 'admin.pacientes.gestionar', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Dar de Alta', 'route' => 'patients.dar-de-alta.index', 'roles' => 'admin,administrador,cirujano,emergencia,internacion', 'order' => 3],
           ['name' => 'Historial de Consultas', 'route' => 'consulta.historial-medico', 'roles' => 'doctor', 'order' => 7],
        ]);

        // 6. Caja Operativa
        $cajaOp = Menu::create([
            'name' => 'Caja Operativa',
            'active_pattern' => 'caja-operativa*',
            'icon_path' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z',
            'color' => 'emerald',
            'roles' => 'caja',
            'order' => 50,
        ]);
        $cajaOp->children()->create(['name' => 'Cobro de Pacientes', 'route' => 'caja.operativa.index', 'order' => 1]);

        // 6.5 Gestionar Clínica (Operativo Médico - admin y administrador)
        $gestionarClinica = Menu::create([
            'name' => 'Gestionar Clínica',
            'active_pattern' => 'admin*,emergency-staff*,quirofano*,internacion-staff*',
            'icon_path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'color' => 'teal',
            'roles' => 'admin,administrador',
            'order' => 45,
        ]);

        // 1. Configuración General (Nivel 2)
        $configGeneral = $gestionarClinica->children()->create([
            'name' => 'Configuración General',
            'active_pattern' => 'admin.especialidades*,admin.doctors*,admin.almacen-medicamentos*',
            'roles' => 'admin,administrador',
            'order' => 1,
        ]);
        $configGeneral->children()->createMany([
            ['name' => 'Especialidades', 'route' => 'admin.especialidades.index', 'roles' => 'admin,administrador', 'order' => 1],
            ['name' => 'Doctores', 'route' => 'admin.doctors.index', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Almacén Medicamentos', 'route' => 'admin.almacen-medicamentos.index', 'roles' => 'admin,administrador', 'order' => 3],
            ['name' => 'Consulta Externa', 'route' => 'admin.consulta-externa-gestion', 'roles' => 'admin,administrador', 'order' => 4],
            ['name' => 'Procedimientos', 'route' => 'admin.procedimientos.index', 'roles' => 'admin,administrador', 'order' => 5],
        ]);

        // 2. Emergencias (Nivel 2)
        $emergenciasAdmin = $gestionarClinica->children()->create([
            'name' => 'Emergencias',
            'active_pattern' => 'admin.emergencies*,emergency-staff*',
            'roles' => 'admin,administrador',
            'order' => 2,
        ]);
        $emergenciasAdmin->children()->createMany([
            ['name' => 'Dashboard', 'route' => 'emergency-staff.dashboard', 'roles' => 'admin,administrador', 'order' => 1],
            ['name' => 'Gestión Emergencias', 'route' => 'admin.emergencies.index', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'emergency-staff.medicamentos.index', 'roles' => 'admin,administrador', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'emergency-staff.enfermeras.index', 'roles' => 'admin,administrador', 'order' => 4],
            ['name' => 'Camillas', 'route' => 'admin.camillas.index', 'roles' => 'admin,administrador', 'order' => 5],
        ]);

        // 3. Cirugías (Nivel 2)
        $cirugiasAdmin = $gestionarClinica->children()->create([
            'name' => 'Cirugías',
            'active_pattern' => 'quirofano*,quirofanos-management*,admin.cirujanos*',
            'roles' => 'admin,administrador',
            'order' => 3,
        ]);
        $cirugiasAdmin->children()->createMany([
            ['name' => 'Panel de Cirugías', 'route' => 'quirofano.index', 'roles' => 'admin,administrador', 'order' => 1],
            ['name' => 'Gestionar Quirófanos', 'route' => 'quirofanos.management.index', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Ver Cirujanos', 'route' => 'admin.cirujanos.index', 'roles' => 'admin,administrador', 'order' => 3],
            ['name' => 'Medicamentos', 'route' => 'quirofano.medicamentos.index', 'roles' => 'admin,administrador', 'order' => 4],
        ]);

        // 4. Internación (Nivel 2)
        $internacionAdmin = $gestionarClinica->children()->create([
            'name' => 'Internación',
            'active_pattern' => 'internacion-staff*',
            'roles' => 'admin,administrador',
            'order' => 4,
        ]);
        $internacionAdmin->children()->createMany([
            ['name' => 'Dashboard', 'route' => 'internacion-staff.dashboard', 'roles' => 'admin,administrador', 'order' => 1],
            ['name' => 'Habitaciones', 'route' => 'internacion-staff.habitaciones.index', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'internacion-staff.medicamentos.index', 'roles' => 'admin,administrador', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'internacion-staff.enfermeras.index', 'roles' => 'admin,administrador', 'order' => 4],
            ['name' => 'Catering', 'route' => 'internacion-staff.catering.gestion', 'roles' => 'admin,administrador', 'order' => 5],
        ]);

        // 5. UTI (Nivel 2)
        $utiAdmin = $gestionarClinica->children()->create([
            'name' => 'UTI',
            'active_pattern' => 'uti*,admin.camillas*',
            'roles' => 'admin,administrador',
            'order' => 5,
        ]);
        $utiAdmin->children()->createMany([
            ['name' => 'Camillas', 'route' => 'admin.camillas.index', 'roles' => 'admin,administrador', 'order' => 1],
            ['name' => 'Panel UTI', 'route' => 'uti.dashboard', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'uti.operativa.medicamentos.readonly', 'roles' => 'admin,administrador', 'order' => 3],
        ]);

        // 7. Administración (Financiera - admin y administrador)
        $admin = Menu::create([
            'name' => 'Administración',
            'active_pattern' => 'caja.gestion*,admin.tarifarios*,admin.seguros*,admin.ingreso-precios*,admin.almacen-inventario*,admin.cuentas*',
            'icon_path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'purple',
            'roles' => 'admin,administrador',
            'order' => 50,
        ]);

        $admin->children()->createMany([
            // Configuración Financiera
            ['name' => 'Tarifarios', 'route' => 'admin.tarifarios', 'roles' => 'admin,administrador', 'order' => 1],
            ['name' => 'Seguros', 'route' => 'admin.seguros', 'roles' => 'admin,administrador', 'order' => 2],
            ['name' => 'Precios de Ingresos', 'route' => 'admin.ingreso-precios.index', 'roles' => 'admin,administrador', 'order' => 3],

            // Inventario y Caja
            ['name' => 'Almacén Inventario', 'route' => 'admin.almacen-inventario.index', 'roles' => 'admin,administrador', 'order' => 10],
            ['name' => 'Control de Caja', 'route' => 'caja.gestion.index', 'roles' => 'admin,administrador', 'order' => 11],

            // Gestión Financiera
            ['name' => 'Cuentas por Cobrar', 'route' => 'admin.cuentas', 'roles' => 'admin,administrador', 'order' => 20],
        ]);

        // 8. Farmacia (Operativo - rol farmacia/admin/administrador)
        $farmacia = Menu::create([
            'name' => 'Farmacia',
            'active_pattern' => 'farmacia*',
            'icon_path' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'color' => 'yellow',
            'roles' => 'farmacia,admin,administrador',
            'order' => 40,
        ]);

        $farmacia->children()->createMany([
            ['name' => 'Dashboard', 'route' => 'farmacia.index', 'roles' => 'farmacia,admin,administrador', 'order' => 1],
            ['name' => 'Punto de Venta', 'route' => 'farmacia.pos', 'roles' => 'farmacia,admin,administrador', 'order' => 2],
            ['name' => 'Inventario', 'route' => 'farmacia.inventario', 'roles' => 'farmacia,admin,administrador', 'order' => 3],
            ['name' => 'Clientes', 'route' => 'farmacia.clientes', 'roles' => 'farmacia,admin,administrador', 'order' => 4],
            ['name' => 'Ventas', 'route' => 'farmacia.ventas', 'roles' => 'farmacia,admin,administrador', 'order' => 5],
        ]);

        // 10. Quirófano (Cirujano)
        $quirofano = Menu::create([
            'name' => 'Quirófano',
            'active_pattern' => 'quirofano*,quirofanos-management*',
            'icon_path' => 'M14.121 14.121L19 19m-7-7l7-7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'color' => 'teal',
            'roles' => 'cirujano',
            'order' => 82,
        ]);
        $quirofano->children()->createMany([
            ['name' => 'Panel de Cirugías', 'route' => 'quirofano.index', 'roles' => 'cirujano', 'order' => 1],
            ['name' => 'Historial', 'route' => 'quirofano.historial', 'roles' => 'cirujano', 'order' => 4],
            ['name' => 'Medicamentos', 'route' => 'quirofano.medicamentos.index', 'roles' => 'cirujano', 'order' => 5],
        ]);

        // 11. Internación (Operativo - rol internacion)
        $internacion = Menu::create([
            'name' => 'Internación',
            'active_pattern' => 'internacion-staff*',
            'icon_path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'color' => 'indigo',
            'roles' => 'internacion',
            'order' => 85,
        ]);

        $internacion->children()->createMany([
            ['name' => 'Panel Principal', 'route' => 'internacion-staff.dashboard', 'roles' => 'internacion', 'order' => 1],
            ['name' => 'Registrar Habitación', 'route' => 'internacion-staff.habitaciones.registro-uso', 'roles' => 'internacion', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'internacion-staff.medicamentos.index', 'roles' => 'internacion', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'internacion-staff.enfermeras.index', 'roles' => 'internacion', 'order' => 4],
            ['name' => 'Catering', 'route' => 'internacion-staff.catering.index', 'roles' => 'internacion,enfermera-internacion', 'order' => 5],
            ]);

        // 12. Gerencial
        $gerencial = Menu::create([
            'name' => 'Gerencial',
            'active_pattern' => 'gerencial*',
            'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'color' => 'orange',
            'roles' => 'admin,gerente,administrador',
            'order' => 60,
        ]);
        $gerencial->children()->createMany([
            ['name' => 'Reportes', 'route' => 'gerencial.reportes', 'roles' => 'admin,gerente,administrador', 'order' => 1],
            ['name' => 'KPIs', 'route' => 'gerencial.kpis', 'roles' => 'admin,gerente,administrador', 'order' => 2],
        ]);

        // 13. Seguridad
        $seguridad = Menu::create([
            'name' => 'Seguridad',
            'active_pattern' => 'seguridad*,user-management*,menus*',
            'icon_path' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
            'color' => 'slate',
            'roles' => 'admin,gerente,administrador',
            'order' => 70,
        ]);
        $seguridad->children()->createMany([
            ['name' => 'Gestión de Usuarios', 'route' => 'user-management.index', 'roles' => 'admin,gerente,administrador', 'order' => 1],
            ['name' => 'Bitácora de Actividades', 'route' => 'seguridad.activity-logs.index', 'roles' => 'admin,gerente,administrador', 'order' => 2],
            // Agregamos el submenú aquí:
            ['name' => 'Gestión de Menús', 'route' => 'menus.index', 'roles' => 'admin', 'order' => 3],
            ['name' => 'Control de Accesos', 'route' => 'seguridad.accesos.index', 'roles' => 'admin,gerente,administrador', 'order' => 4],
        ]);
    }
}