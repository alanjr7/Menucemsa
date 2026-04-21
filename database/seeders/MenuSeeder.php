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
            ['name' => 'Pendientes', 'route' => 'emergency-staff.pending', 'roles' => 'enfermera-emergencia', 'order' => 2],
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
            ['name' => 'Historial', 'route' => 'internacion-staff.historial-general', 'roles' => 'enfermera-internacion', 'order' => 2],
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
            ['name' => 'Pendientes', 'route' => 'emergency-staff.pending', 'roles' => 'emergencia', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'emergency-staff.medicamentos.index', 'roles' => 'emergencia,admin', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'emergency-staff.enfermeras.index', 'roles' => 'emergencia,admin', 'order' => 4],
        ]);

        // 4.5 Administración de Emergencias (Admin/Director Médico)
        $adminEmergencias = Menu::create([
            'name' => 'Admin Emergencias',
            'active_pattern' => 'emergency-staff*,admin/emergencies*',
            'icon_path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'color' => 'rose',
            'roles' => 'admin,dir_medico',
            'order' => 30,
        ]);

        $adminEmergencias->children()->createMany([
            ['name' => 'Dashboard', 'route' => 'emergency-staff.dashboard', 'roles' => 'admin,dir_medico', 'order' => 1],
            ['name' => 'Gestión Emergencias', 'route' => 'admin.emergencies.index', 'roles' => 'admin,dir_medico', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'emergency-staff.medicamentos.index', 'roles' => 'admin', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'emergency-staff.enfermeras.index', 'roles' => 'admin', 'order' => 4],
        ]);

        // 5. Pacientes (Con Submenús) - Admin/Director Médico/Doctor
        $pacientes = Menu::create([
            'name' => 'Pacientes',
            'active_pattern' => 'patients*,consulta*,uti*,quirofano*,admin/emergencies*',
            'icon_path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'color' => 'blue',
            'roles' => 'admin,dir_medico,doctor',
            'order' => 20,
        ]);

        $pacientes->children()->createMany([
            ['name' => 'Maestro de Pacientes', 'route' => 'patients.index', 'roles' => 'admin,dir_medico,doctor', 'order' => 1],
            ['name' => 'UTI - Administración', 'route' => 'uti.admin.index', 'roles' => 'admin,dir_medico', 'order' => 2],
            ['name' => 'Quirófano', 'route' => 'quirofano.index', 'roles' => 'admin,dir_medico,cirujano', 'order' => 3],
            ['name' => 'Gestión de Emergencias', 'route' => 'admin.emergencies.index', 'roles' => 'admin,dir_medico', 'order' => 4],
            ['name' => 'Consulta Externa', 'route' => 'consulta.index', 'roles' => 'doctor', 'order' => 5],
            ['name' => 'Historial de Consultas', 'route' => 'consulta.historial-medico', 'roles' => 'doctor', 'order' => 6],
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

        // 7. Administración (Master Menu - solo admin)
        $admin = Menu::create([
            'name' => 'Administración',
            'active_pattern' => 'caja*,facturacion*,admin*,farmacias*,medicamentos*',
            'icon_path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'purple',
            'roles' => 'admin',
            'order' => 50,
        ]);

        $admin->children()->createMany([
            // Configuración General
            ['name' => 'Especialidades', 'route' => 'admin.especialidades.index', 'order' => 1],
            ['name' => 'Doctores', 'route' => 'admin.doctors.index', 'order' => 2],
            ['name' => 'Tarifarios', 'route' => 'admin.tarifarios', 'order' => 3],
            ['name' => 'Seguros', 'route' => 'admin.seguros', 'order' => 4],

            // Gestión Hospitalaria
            ['name' => 'Almacén Central', 'route' => 'admin.almacen-medicamentos.index', 'order' => 10],
            ['name' => 'Farmacias', 'route' => 'farmacias.index', 'order' => 11],
            ['name' => 'Medicamentos', 'route' => 'medicamentos.index', 'order' => 12],
            ['name' => 'Control de Caja', 'route' => 'caja.gestion.index', 'order' => 13],

            // Gestión Operativa y Financiera
            ['name' => 'Gestionar Consulta Externa', 'route' => 'admin.consulta-externa-gestion', 'order' => 20],
            ['name' => 'Facturación', 'route' => 'admin.facturacion.index', 'order' => 21],
            ['name' => 'Cuentas por Cobrar', 'route' => 'admin.cuentas', 'order' => 22],
        ]);

        // 8. Farmacia (Operativo - rol farmacia)
        $farmacia = Menu::create([
            'name' => 'Farmacia',
            'active_pattern' => 'farmacia*',
            'icon_path' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'color' => 'yellow',
            'roles' => 'farmacia',
            'order' => 40,
        ]);

        $farmacia->children()->createMany([
            ['name' => 'Dashboard', 'route' => 'farmacia.index', 'roles' => 'farmacia', 'order' => 1],
            ['name' => 'Punto de Venta', 'route' => 'farmacia.pos', 'roles' => 'farmacia', 'order' => 2],
            ['name' => 'Inventario', 'route' => 'farmacia.inventario', 'roles' => 'farmacia', 'order' => 3],
            ['name' => 'Clientes', 'route' => 'farmacia.clientes', 'roles' => 'farmacia', 'order' => 4],
            ['name' => 'Ventas', 'route' => 'farmacia.ventas', 'roles' => 'farmacia', 'order' => 5],
        ]);

        // 9. UTI - Terapia Intensiva
        $uti = Menu::create([
            'name' => 'UTI - Terapia Intensiva',
            'active_pattern' => 'uti*',
            'icon_path' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'color' => 'cyan',
            'roles' => 'uti',
            'order' => 80,
        ]);
        $uti->children()->create(['name' => 'Panel de Pacientes', 'route' => 'uti.operativa.index', 'order' => 1]);

        // 10. Quirófano (Cirujano)
        $quirofano = Menu::create([
            'name' => 'Quirófano',
            'active_pattern' => 'quirofano*,quirofanos-management*',
            'icon_path' => 'M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z',
            'color' => 'teal',
            'roles' => 'cirujano',
            'order' => 82,
        ]);
        $quirofano->children()->createMany([
            ['name' => 'Panel de Cirugías', 'route' => 'quirofano.index', 'order' => 1],
            ['name' => 'Calendario', 'route' => 'quirofano.calendario', 'order' => 2],
            ['name' => 'Historial', 'route' => 'quirofano.historial', 'order' => 3],
            ['name' => 'Medicamentos', 'route' => 'quirofano.medicamentos.index', 'order' => 4],
            ['name' => 'Gestionar Quirófanos', 'route' => 'quirofanos.management.index', 'order' => 5],
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
            ['name' => 'Habitaciones', 'route' => 'internacion-staff.habitaciones.index', 'roles' => 'internacion', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'internacion-staff.medicamentos.index', 'roles' => 'internacion,admin', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'internacion-staff.enfermeras.index', 'roles' => 'internacion,admin', 'order' => 4],
            ['name' => 'Historial', 'route' => 'internacion-staff.historial-general', 'roles' => 'internacion', 'order' => 5],
        ]);

        // 11.5 Administración de Internación (Admin/Director Médico)
        $adminInternacion = Menu::create([
            'name' => 'Admin Internación',
            'active_pattern' => 'hospitalizacion*,internacion-staff/habitaciones*',
            'icon_path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            'color' => 'blue',
            'roles' => 'admin,dir_medico',
            'order' => 35,
        ]);

        $adminInternacion->children()->createMany([
            ['name' => 'Hospitalización', 'route' => 'hospitalizacion.index', 'roles' => 'admin,dir_medico', 'order' => 1],
            ['name' => 'Gestión Habitaciones', 'route' => 'internacion-staff.habitaciones.index', 'roles' => 'admin,dir_medico', 'order' => 2],
            ['name' => 'Medicamentos', 'route' => 'internacion-staff.medicamentos.index', 'roles' => 'admin', 'order' => 3],
            ['name' => 'Enfermeras', 'route' => 'internacion-staff.enfermeras.index', 'roles' => 'admin', 'order' => 4],
        ]);

        // 12. Gerencial
        $gerencial = Menu::create([
            'name' => 'Gerencial',
            'active_pattern' => 'gerencial*',
            'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'color' => 'orange',
            'roles' => 'admin,gerente',
            'order' => 60,
        ]);
        $gerencial->children()->createMany([
            ['name' => 'Reportes', 'route' => 'gerencial.reportes', 'order' => 1],
            ['name' => 'KPIs', 'route' => 'gerencial.kpis', 'order' => 2],
        ]);

        // 13. Seguridad
        $seguridad = Menu::create([
            'name' => 'Seguridad',
            'active_pattern' => 'seguridad*,user-management*,menus*',
            'icon_path' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
            'color' => 'slate',
            'roles' => 'admin,gerente',
            'order' => 70,
        ]);
        $seguridad->children()->createMany([
            ['name' => 'Gestión de Usuarios', 'route' => 'user-management.index', 'order' => 1],
            ['name' => 'Bitácora de Actividades', 'route' => 'seguridad.activity-logs.index', 'order' => 2],
            // Agregamos el submenú aquí:
            ['name' => 'Gestión de Menús', 'route' => 'menus.index', 'roles' => 'admin', 'order' => 3],
        ]);
    }
}