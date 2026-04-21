# 📋 Rutas del Sistema Menucemsa - Organizado por Rol

> **Archivo analizado:** `routes/web.php`  
> **Fecha de generación:** Abril 2026  
> **Roles cubiertos:** reception, emergencia, internacion, caja, farmacia, cirugia/quirofano, uti, doctor, admin, gerencia, seguridad

---

## 👤 1. ROL: RECEPCIÓN (`reception`)

**Middleware:** `auth, role:admin|reception|dirmedico`

### Rutas Principales

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/reception` | `reception` | GET | Dashboard recepción - vista general del módulo |
| `/admision` | `admision.index` | GET | Vista de admisión de pacientes |
| `/patients` | `patients.index` | GET | Lista completa de pacientes registrados |
| `/patients/{ci}` | `patients.show` | GET | Ver ficha detallada de un paciente por CI |

### Sub-módulos de Recepción

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/reception/consulta-externa` | `reception.consulta-externa` | GET | Registrar pacientes para consulta externa |
| `/reception/emergencia` | `reception.emergencia` | GET | Ingreso de pacientes a emergencias |
| `/reception/hospitalizacion` | `reception.hospitalizacion` | GET | Ingreso de pacientes a hospitalización |

### APIs Recepción

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/api/buscar-paciente` | `reception.buscar-paciente` | POST | Buscar paciente existente por CI |
| `/api/registrar-consulta-externa` | `reception.registrar-consulta` | POST | Registrar nueva consulta externa |
| `/api/triage-general` | `reception.triage-general` | POST | Procesar triage general |
| `/api/emergency-ingreso` | `reception.emergency-ingreso` | POST | Registrar ingreso a emergencia |
| `/api/emergency-activas` | `reception.emergency-activas` | GET | Listar emergencias activas |
| `/api/registrar-emergencia` | `reception.registrar-emergencia` | POST | Registrar nueva emergencia |
| `/api/registrar-hospitalizacion` | `reception.registrar-hospitalizacion` | POST | Registrar hospitalización |
| `/api/hospitalizaciones-activas` | `reception.hospitalizaciones-activas` | GET | Listar hospitalizaciones activas |
| `/api/agenda-dia` | `reception.agenda-dia` | GET | Obtener agenda del día |
| `/api/nueva-cita` | `reception.nueva-cita` | POST | Crear nueva cita médica |
| `/api/estadisticas-dashboard` | `reception.estadisticas` | GET | Estadísticas para dashboard |

---

## 🚨 2. ROL: EMERGENCIA (`emergencia`)

**Roles incluidos:** `emergencia|enfermera-emergencia|admin|dirmedico`

**Prefix:** `/emergency-staff`  
**Name:** `emergency-staff.`

### Menú: Emergencias (rol: `emergencia`)

| Submenú | Ruta | Descripción |
|---------|------|-------------|
| Panel Principal | `emergency-staff.dashboard` | Dashboard de emergencias |
| Pendientes | `emergency-staff.pending` | Emergencias pendientes de atención |
| Medicamentos | `emergency-staff.medicamentos.index` | Inventario de medicamentos (emergencia,admin) |
| Enfermeras | `emergency-staff.enfermeras.index` | Gestión de enfermeras (emergencia,admin) |

### Menú: Admin Emergencias (rol: `admin,dir_medico`)

| Submenú | Ruta | Descripción |
|---------|------|-------------|
| Dashboard | `emergency-staff.dashboard` | Dashboard de emergencias |
| Gestión Emergencias | `admin.emergencies.index` | Vista administrativa de emergencias |
| Medicamentos | `emergency-staff.medicamentos.index` | Gestión de medicamentos (solo admin) |
| Enfermeras | `emergency-staff.enfermeras.index` | Gestión de enfermeras (solo admin) |

### Dashboard y Gestión

| Ruta | Nombre | Método | Descripción | Acceso |
|------|--------|--------|-------------|--------|
| `/emergency-staff/dashboard` | `emergency-staff.dashboard` | GET | Dashboard principal de emergencias | emergencia, enfermera-emergencia, admin, dirmedico |
| `/emergency-staff/create` | `emergency-staff.create` | GET | Formulario crear emergencia | emergencia, admin, dirmedico |
| `/emergency-staff/pending` | `emergency-staff.pending` | GET | Emergencias pendientes de atención | Todos |
| `/emergency-staff/{emergency}` | `emergency-staff.show` | GET | Ver detalle de emergencia | Todos |
| `/emergency-staff/{emergency}/evaluacion` | `emergency-staff.evaluacion` | GET | Evaluar paciente | Todos |
| `/emergency-staff/{emergency}/guardar-evaluacion` | `emergency-staff.guardar-evaluacion` | POST | Guardar evaluación médica | Todos |
| `/emergency-staff/{emergency}/historial` | `emergency-staff.historial` | GET | Ver historial de atenciones | Todos |
| `/emergency-staff/{emergency}/update-status` | `emergency-staff.update-status` | POST | Actualizar estado de emergencia | emergencia, admin, dirmedico |
| `/emergency-staff/{emergency}/derivar` | `emergency-staff.derivar` | POST | Derivar a cirugía/UTI/hospitalización | emergencia, admin, dirmedico |
| `/emergency-staff/{emergency}/alta` | `emergency-staff.alta` | POST | Dar de alta paciente | emergencia, admin, dirmedico |

### Medicamentos de Emergencia (SOLO `admin|emergencia`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/emergency-staff/medicamentos` | `emergency-staff.medicamentos.index` | GET | Listar inventario de medicamentos |
| `/emergency-staff/medicamentos/create` | `emergency-staff.medicamentos.create` | GET | Agregar medicamento |
| `/emergency-staff/medicamentos` | `emergency-staff.medicamentos.store` | POST | Guardar nuevo medicamento |
| `/emergency-staff/medicamentos/{medicamento}` | `emergency-staff.medicamentos.show` | GET | Ver detalle de medicamento |
| `/emergency-staff/medicamentos/{medicamento}/edit` | `emergency-staff.medicamentos.edit` | GET | Editar medicamento |
| `/emergency-staff/medicamentos/{medicamento}` | `emergency-staff.medicamentos.update` | PUT | Actualizar medicamento |
| `/emergency-staff/medicamentos/{medicamento}/stock` | `emergency-staff.medicamentos.stock` | POST | Actualizar stock |

### Enfermeras de Emergencia (SOLO `admin|emergencia`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/emergency-staff/enfermeras` | `emergency-staff.enfermeras.index` | GET | Listar enfermeras |
| `/emergency-staff/enfermeras/create` | `emergency-staff.enfermeras.create` | GET | Crear enfermera |
| `/emergency-staff/enfermeras` | `emergency-staff.enfermeras.store` | POST | Guardar enfermera |
| `/emergency-staff/enfermeras/{enfermera}` | `emergency-staff.enfermeras.show` | GET | Ver enfermera |
| `/emergency-staff/enfermeras/{enfermera}/edit` | `emergency-staff.enfermeras.edit` | GET | Editar enfermera |
| `/emergency-staff/enfermeras/{enfermera}` | `emergency-staff.enfermeras.update` | PUT | Actualizar enfermera |
| `/emergency-staff/enfermeras/{enfermera}/toggle-status` | `emergency-staff.enfermeras.toggle-status` | PATCH | Activar/Desactivar enfermera |
| `/emergency-staff/enfermeras/{enfermera}/permissions` | `emergency-staff.enfermeras.permissions` | GET/POST | Gestionar permisos |

### APIs Emergencia

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/emergency-staff/api/emergencias` | `emergency-staff.api.emergencias` | GET | API listado de emergencias |
| `/emergency-staff/api/estadisticas` | `emergency-staff.api.estadisticas` | GET | API estadísticas |
| `/emergency-staff/api/medicamentos-disponibles` | `emergency-staff.api.medicamentos` | GET | API medicamentos disponibles |
| `/emergency-staff/api/mis-permisos` | `emergency-staff.api.permisos` | GET | API permisos del usuario actual |

---

## 🏥 3. ROL: INTERNACIÓN (`internacion`)

**Roles incluidos:** `internacion|enfermera-internacion|admin|dirmedico`

**Prefix:** `/internacion-staff`  
**Name:** `internacion-staff.`

### Menú: Internación (rol: `internacion`)

| Submenú | Ruta | Descripción |
|---------|------|-------------|
| Panel Principal | `internacion-staff.dashboard` | Dashboard de internación |
| Habitaciones | `internacion-staff.habitaciones.index` | Gestión de habitaciones y camas |
| Medicamentos | `internacion-staff.medicamentos.index` | Inventario de medicamentos (admin/internacion) |
| Enfermeras | `internacion-staff.enfermeras.index` | Gestión de enfermeras (admin/internacion) |
| Historial | `internacion-staff.historial-general` | Historial de internaciones |

### Menú: Admin Internación (rol: `admin,dir_medico`)

| Submenú | Ruta | Descripción |
|---------|------|-------------|
| Hospitalización | `hospitalizacion.index` | Vista administrativa de hospitalización |
| Gestión Habitaciones | `internacion-staff.habitaciones.index` | Administración de habitaciones |
| Medicamentos | `internacion-staff.medicamentos.index` | Gestión de medicamentos (solo admin) |
| Enfermeras | `internacion-staff.enfermeras.index` | Gestión de enfermeras (solo admin) |

### Dashboard y Gestión

| Ruta | Nombre | Método | Descripción | Acceso |
|------|--------|--------|-------------|--------|
| `/internacion-staff/dashboard` | `internacion-staff.dashboard` | GET | Dashboard internación | Todos |
| `/internacion-staff/evaluar/{id}` | `internacion-staff.evaluar` | GET | Evaluar paciente internado | Todos |
| `/internacion-staff/historial/{id}` | `internacion-staff.historial` | GET | Ver historial del paciente | Todos |
| `/internacion-staff/historial-general` | `internacion-staff.historial-general` | GET | Historial general de internaciones | Todos |
| `/internacion-staff/export-historial` | `internacion-staff.export-historial` | GET | Exportar historial | Todos |

### Habitaciones y Camas

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/internacion-staff/habitaciones` | `internacion-staff.habitaciones.index` | GET | Listar habitaciones |
| `/internacion-staff/habitaciones/create` | `internacion-staff.habitaciones.create` | GET | Crear habitación |
| `/internacion-staff/habitaciones` | `internacion-staff.habitaciones.store` | POST | Guardar habitación |
| `/internacion-staff/habitaciones/{habitacion}` | `internacion-staff.habitaciones.show` | GET | Ver habitación |
| `/internacion-staff/habitaciones/{habitacion}/edit` | `internacion-staff.habitaciones.edit` | GET | Editar habitación |
| `/internacion-staff/habitaciones/{habitacion}` | `internacion-staff.habitaciones.update` | PUT | Actualizar habitación |
| `/internacion-staff/habitaciones/{habitacion}` | `internacion-staff.habitaciones.destroy` | DELETE | Eliminar habitación |
| `/internacion-staff/habitaciones/{habitacion}/asignar-paciente` | `internacion-staff.habitaciones.asignar-paciente` | POST | Asignar paciente a habitación |
| `/internacion-staff/camas/{cama}/liberar` | `internacion-staff.camas.liberar` | POST | Liberar cama |

### Medicamentos de Internación (SOLO `admin|internacion`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/internacion-staff/medicamentos` | `internacion-staff.medicamentos.index` | GET | Listar medicamentos |
| `/internacion-staff/medicamentos/create` | `internacion-staff.medicamentos.create` | GET | Crear medicamento |
| `/internacion-staff/medicamentos` | `internacion-staff.medicamentos.store` | POST | Guardar medicamento |
| `/internacion-staff/medicamentos/{medicamento}` | `internacion-staff.medicamentos.show` | GET | Ver medicamento |
| `/internacion-staff/medicamentos/{medicamento}/edit` | `internacion-staff.medicamentos.edit` | GET | Editar medicamento |
| `/internacion-staff/medicamentos/{medicamento}` | `internacion-staff.medicamentos.update` | PUT | Actualizar medicamento |
| `/internacion-staff/medicamentos/{medicamento}/stock` | `internacion-staff.medicamentos.stock` | POST | Actualizar stock |

### Enfermeras de Internación (SOLO `admin|internacion`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/internacion-staff/enfermeras` | `internacion-staff.enfermeras.index` | GET | Listar enfermeras |
| `/internacion-staff/enfermeras/create` | `internacion-staff.enfermeras.create` | GET | Crear enfermera |
| `/internacion-staff/enfermeras` | `internacion-staff.enfermeras.store` | POST | Guardar enfermera |
| `/internacion-staff/enfermeras/{enfermera}` | `internacion-staff.enfermeras.show` | GET | Ver enfermera |
| `/internacion-staff/enfermeras/{enfermera}/edit` | `internacion-staff.enfermeras.edit` | GET | Editar enfermera |
| `/internacion-staff/enfermeras/{enfermera}` | `internacion-staff.enfermeras.update` | PUT | Actualizar enfermera |
| `/internacion-staff/enfermeras/{enfermera}/toggle-status` | `internacion-staff.enfermeras.toggle-status` | PATCH | Activar/Desactivar |
| `/internacion-staff/enfermeras/{enfermera}/permissions` | `internacion-staff.enfermeras.permissions` | GET/POST | Gestionar permisos |

### APIs Internación

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/internacion-staff/api/internaciones` | `internacion-staff.api.internaciones` | GET | API listado internaciones |
| `/internacion-staff/api/estadisticas` | `internacion-staff.api.estadisticas` | GET | API estadísticas |
| `/internacion-staff/api/internacion/{id}/update-status` | `internacion-staff.update-status` | POST | Actualizar estado |
| `/internacion-staff/api/internacion/{id}/derivar-uti` | `internacion-staff.derivar-uti` | POST | Derivar a UTI |
| `/internacion-staff/api/internacion/{id}/alta` | `internacion-staff.alta` | POST | Dar de alta |
| `/internacion-staff/api/medicamentos-disponibles` | `internacion-staff.api.medicamentos-disponibles` | GET | API medicamentos |
| `/internacion-staff/api/medicamentos/buscar` | `internacion-staff.api.medicamentos.buscar` | GET | Buscar medicamentos |
| `/internacion-staff/api/internacion/{id}/medicamentos` | `internacion-staff.api.medicamentos` | GET/POST | Medicamentos del paciente |
| `/internacion-staff/api/internacion/{id}/catering` | `internacion-staff.api.catering` | GET/POST | Catering del paciente |
| `/internacion-staff/api/internacion/{id}/drenajes` | `internacion-staff.api.drenajes` | GET/POST | Drenajes del paciente |
| `/internacion-staff/api/internacion/{id}/receta` | `internacion-staff.api.receta.update` | POST | Actualizar receta |

---

## 💰 4. ROL: CAJA (`caja`)

**Roles incluidos:** `caja|admin`

### Caja Operativa (`role:admin|caja`)

**Prefix:** `/caja-operativa`  
**Name:** `caja.operativa.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/caja-operativa/` | `caja.operativa.index` | GET | Dashboard caja operativa |
| `/caja-operativa/abrir` | `caja.operativa.abrir` | POST | Abrir caja del día |
| `/caja-operativa/cerrar` | `caja.operativa.cerrar` | POST | Cerrar caja |
| `/caja-operativa/pacientes-pendientes` | `caja.operativa.pacientes-pendientes` | GET | Listar pacientes por cobrar |
| `/caja-operativa/detalle-cuenta/{id}` | `caja.operativa.detalle-cuenta` | GET | Ver detalle de cuenta |
| `/caja-operativa/procesar-cobro` | `caja.operativa.procesar-cobro` | POST | Procesar cobro de servicio |
| `/caja-operativa/resumen-dia` | `caja.operativa.resumen-dia` | GET | Resumen del día |
| `/caja-operativa/buscar-paciente` | `caja.operativa.buscar-paciente` | GET | Buscar paciente para cobro |
| `/caja-operativa/tarifas` | `caja.operativa.tarifas` | GET | Ver tarifas disponibles |
| `/caja-operativa/uti-pacientes` | `caja.operativa.uti-pacientes` | GET | Pacientes UTI por cobrar |
| `/caja-operativa/uti-detalle-cuenta/{id}` | `caja.operativa.uti-detalle-cuenta` | GET | Detalle cuenta UTI |
| `/caja-operativa/uti-procesar-cobro/{id}` | `caja.operativa.uti-procesar-cobro` | POST | Cobrar UTI |
| `/caja-operativa/uti-deposito/{id}` | `caja.operativa.uti-deposito` | POST | Registrar depósito UTI |

### Gestión de Caja (SOLO `admin`)

**Prefix:** `/caja-gestion`  
**Name:** `caja.gestion.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/caja-gestion/` | `caja.gestion.index` | GET | Dashboard gestión de cajas |
| `/caja-gestion/transacciones` | `caja.gestion.transacciones` | GET | Historial de transacciones |
| `/caja-gestion/transaccion/{id}` | `caja.gestion.detalle-transaccion` | GET | Detalle de transacción |
| `/caja-gestion/control-cajas` | `caja.gestion.control-cajas` | GET | Control de cajas abiertas |
| `/caja-gestion/resumen-financiero` | `caja.gestion.resumen-financiero` | GET | Reportes financieros |
| `/caja-gestion/auditoria` | `caja.gestion.auditoria` | GET | Auditoría de caja |
| `/caja-gestion/datos-facturacion` | `caja.gestion.datos-facturacion` | GET | Configuración de facturación |
| `/caja-gestion/usuarios-caja` | `caja.gestion.usuarios-caja` | GET | Usuarios de caja |

---

## 💊 5. ROL: FARMACIA (`farmacia`)

**Middleware:** `auth, role:admin|farmacia`

**Prefix:** `/farmacia`  
**Name:** `farmacia.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/farmacia/` | `farmacia.index` | GET | Dashboard de farmacia |
| `/farmacia/punto-de-venta` | `farmacia.pos` | GET | Punto de venta - venta de medicamentos |
| `/farmacia/punto-de-venta/procesar` | `farmacia.pos.procesar` | POST | Procesar venta |
| `/farmacia/inventario` | `farmacia.inventario` | GET | Gestión de inventario |
| `/farmacia/inventario` | `farmacia.inventario.store` | POST | Agregar producto al inventario |
| `/farmacia/inventario/{id}` | `farmacia.inventario.update` | PUT | Actualizar producto |
| `/farmacia/inventario/{id}` | `farmacia.inventario.destroy` | DELETE | Eliminar producto |
| `/farmacia/ventas` | `farmacia.ventas` | GET | Historial de ventas |
| `/farmacia/ventas/{codigoVenta}` | `farmacia.ventas.show` | GET | Ver detalle de venta |
| `/farmacia/ventas/{codigoVenta}` | `farmacia.ventas.destroy` | DELETE | Anular venta |
| `/farmacia/clientes` | `farmacia.clientes` | GET | Gestión de clientes |
| `/farmacia/clientes` | `farmacia.clientes.store` | POST | Crear cliente |
| `/farmacia/clientes/{id}` | `farmacia.clientes.update` | PUT | Actualizar cliente |
| `/farmacia/clientes/{id}` | `farmacia.clientes.destroy` | DELETE | Eliminar cliente |
| `/farmacia/reporte` | `farmacia.reporte` | GET | Reportes de farmacia |
| `/farmacia/reporte/filtrar` | `farmacia.reporte.filtrar` | POST | Filtrar reportes |

---

## 🔪 6. ROL: CIRUGÍA/QUIRÓFANO (`cirujano` + admin/reception/dirmedico)

**Middleware:** `auth, role:admin|reception|dirmedico|cirujano`

### Dashboard y Programación

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/quirofano` | `quirofano.index` | GET | Dashboard de quirófano |
| `/quirofano/historial` | `quirofano.historial` | GET | Historial de cirugías realizadas |
| `/quirofano/historial/export` | `quirofano.historial.export` | GET | Exportar historial |
| `/quirofano/create` | `quirofano.create` | GET | Formulario programar cirugía |
| `/quirofano` | `quirofano.store` | POST | Guardar programación |
| `/quirofano/calendario` | `quirofano.calendario` | GET | Vista calendario de cirugías |
| `/quirofano/{cita}` | `quirofano.show` | GET | Ver detalle de cirugía programada |
| `/quirofano/{cita}/edit` | `quirofano.edit` | GET | Editar cirugía |
| `/quirofano/{cita}` | `quirofano.update` | PUT | Actualizar cirugía |
| `/quirofano/{cita}/iniciar` | `quirofano.iniciar` | POST | Iniciar cirugía |
| `/quirofano/{cita}/finalizar` | `quirofano.finalizar` | POST | Finalizar cirugía |
| `/quirofano/{cita}/cancelar` | `quirofano.cancelar` | POST | Cancelar cirugía |

### Cirugías desde Emergencia

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/quirofano/emergencia/{emergency_id}/programar` | `quirofano.programar-emergencia` | GET | Programar cirugía desde emergencia |
| `/quirofano/emergencia/store` | `quirofano.store-emergencia` | POST | Guardar cirugía de emergencia |
| `/quirofano/emergencia/{emergency_id}/iniciar` | `quirofano.iniciar-emergencia` | POST | Iniciar cirugía de emergencia |

### APIs Quirófano

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/quirofano/disponibilidad` | `quirofano.disponibilidad` | POST | Verificar disponibilidad quirófano |
| `/quirofano/api/dashboard` | `quirofano.api.dashboard` | GET | API datos dashboard |
| `/api/quirofanos-disponibles` | `api.quirofanos-disponibles` | GET | Listar quirófanos disponibles |
| `/api/paciente/{ci}` | `api.paciente` | GET | Datos de paciente |
| `/api/medico/{ci}` | `api.medico` | GET | Datos de médico |
| `/api/pacientes-lista` | `api.pacientes-lista` | GET | Lista de pacientes |
| `/api/medicos-lista` | `api.medicos-lista` | GET | Lista de médicos |
| `/quirofano/api/medicos-disponibles` | `quirofano.medicos-disponibles` | GET | Médicos disponibles |

### Gestión de Quirófanos (SOLO `admin|cirujano`)

**Middleware adicional:** `role:admin|cirujano`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/quirofanos-management` | `quirofanos.management.index` | GET | Gestionar quirófanos |
| `/quirofanos-management/create` | `quirofanos.management.create` | GET | Crear quirófano |
| `/quirofanos-management` | `quirofanos.management.store` | POST | Guardar quirófano |
| `/quirofanos-management/{quirofano}` | `quirofanos.management.show` | GET | Ver quirófano |
| `/quirofanos-management/{quirofano}/edit` | `quirofanos.management.edit` | GET | Editar quirófano |
| `/quirofanos-management/{quirofano}` | `quirofanos.management.update` | PUT | Actualizar quirófano |
| `/quirofanos-management/{quirofano}` | `quirofanos.management.destroy` | DELETE | Eliminar quirófano |
| `/quirofanos-management/{quirofano}/estado` | `quirofanos.management.estado` | POST | Cambiar estado quirófano |
| `/api/quirofanos/next-number` | `quirofanos.api.next-number` | GET | Siguiente número de quirófano |

### Medicamentos de Quirófano (SOLO `admin|cirujano`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/quirofano/medicamentos` | `quirofano.medicamentos.index` | GET | Listar medicamentos quirófano |
| `/quirofano/medicamentos/create` | `quirofano.medicamentos.create` | GET | Crear medicamento |
| `/quirofano/medicamentos` | `quirofano.medicamentos.store` | POST | Guardar medicamento |
| `/quirofano/medicamentos/{medicamento}` | `quirofano.medicamentos.show` | GET | Ver medicamento |
| `/quirofano/medicamentos/{medicamento}/edit` | `quirofano.medicamentos.edit` | GET | Editar medicamento |
| `/quirofano/medicamentos/{medicamento}` | `quirofano.medicamentos.update` | PUT | Actualizar medicamento |
| `/quirofano/medicamentos/{medicamento}` | `quirofano.medicamentos.destroy` | DELETE | Eliminar medicamento |
| `/quirofano/medicamentos/{medicamento}/stock` | `quirofano.medicamentos.stock` | POST | Actualizar stock |
| `/quirofano/{cita}/medicamentos-disponibles` | `quirofano.medicamentos.disponibles` | GET | Medicamentos disponibles para cirugía |
| `/quirofano/{cita}/medicamentos-usados` | `quirofano.medicamentos.usados` | GET | Medicamentos usados en cirugía |
| `/quirofano/{cita}/medicamentos` | `quirofano.medicamentos.agregar` | POST | Agregar medicamento a cirugía |

---

## 🧪 7. ROL: UTI (Unidad de Terapia Intensiva)

**Roles incluidos:** Varios niveles de acceso

### UTI Operativo (`admin|dirmedico|doctor|uti`)

**Prefix:** `/uti-operativo`  
**Name:** `uti.operativa.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/uti-operativo/` | `uti.operativa.index` | GET | Dashboard UTI operativo |
| `/uti-operativo/paciente/{id}` | `uti.operativa.paciente.show` | GET | Ficha del paciente UTI |

### Medicamentos UTI (SOLO `admin|uti`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/uti-operativo/medicamentos` | `uti.operativa.medicamentos.index` | GET | Listar medicamentos UTI |
| `/uti-operativo/medicamentos/create` | `uti.operativa.medicamentos.create` | GET | Crear medicamento |
| `/uti-operativo/medicamentos` | `uti.operativa.medicamentos.store` | POST | Guardar medicamento |
| `/uti-operativo/medicamentos/{medicamento}` | `uti.operativa.medicamentos.show` | GET | Ver medicamento |
| `/uti-operativo/medicamentos/{medicamento}/edit` | `uti.operativa.medicamentos.edit` | GET | Editar medicamento |
| `/uti-operativo/medicamentos/{medicamento}` | `uti.operativa.medicamentos.update` | PUT | Actualizar medicamento |
| `/uti-operativo/medicamentos/{medicamento}` | `uti.operativa.medicamentos.destroy` | DELETE | Eliminar medicamento |
| `/uti-operativo/medicamentos/{medicamento}/stock` | `uti.operativa.medicamentos.stock` | POST | Actualizar stock |

### UTI Administración (SOLO `admin`)

**Prefix:** `/uti-admin`  
**Name:** `uti.admin.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/uti-admin/` | `uti.admin.index` | GET | Dashboard administración UTI |
| `/uti-admin/camas` | `uti.admin.camas` | GET | Gestión de camas UTI |
| `/uti-admin/control-financiero` | `uti.admin.control-financiero` | GET | Control financiero UTI |
| `/uti-admin/tarifario` | `uti.admin.tarifario` | GET | Tarifario UTI |

### UTI Recepción (`admin|reception|dirmedico`)

**Prefix:** `/reception/uti`  
**Name:** `reception.uti.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/reception/uti/ingreso` | `reception.uti.ingreso` | GET | Ingreso de paciente a UTI |

---

## 👨‍⚕️ 8. ROL: DOCTOR/MÉDICO (`doctor`)

**Middleware:** `role:admin|dirmedico|doctor`

### Dashboard y Áreas Médicas

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/uti` | `uti.index` | GET | Dashboard UTI médico |
| `/hospitalizacion` | `hospitalizacion.index` | GET | Dashboard hospitalización médico |
| `/medico/dashboard` | `medico.dashboard` | GET | Dashboard personal del médico |
| `/medico/atender-paciente` | `medico.atender-paciente` | POST | Atender paciente asignado |

### Consulta Externa (`doctor|dirmedico|admin`)

**Middleware:** `role:doctor|dirmedico|admin`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/consulta-externa` | `consulta.index` | GET | Lista de consultas del médico |
| `/consulta/{consultaCodigo}` | `consulta.ver` | GET | Ver detalle de consulta |
| `/consulta-externa/iniciar/{consultaId}` | `consulta.iniciar` | POST | Iniciar atención de consulta |
| `/consulta-externa/completar/{consultaId}` | `consulta.completar` | POST | Completar/finalizar consulta |
| `/api/paciente/{ci}` | `consulta.paciente` | GET | API datos del paciente |

### Historiales y Control (admin)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/consulta-externa/historial/{ci_medico?}` | `consulta.historial-medico` | GET | Ver historial médico |
| `/consulta-externa/pacientes/{ci_medico?}` | `consulta.pacientes-medicos` | GET | Ver pacientes por médico |
| `/admin/consulta-externa-gestion` | `admin.consulta-externa-gestion` | GET | Vista control total consultas (admin) |

---

## ⚙️ 9. ROL: ADMINISTRADOR (`admin`)

**Middleware:** `role:admin` (algunas compartidas con `caja`)

### Menú: Administración (rol: `admin`)

| Categoría | Submenú | Ruta |
|-----------|---------|------|
| **Gestión Médica** | Especialidades | `admin.especialidades.index` |
| **Gestión Médica** | Doctores | `admin.doctors.index` |
| **Gestión Financiera** | Facturación | `admin.facturacion.index` |
| **Gestión Financiera** | Tarifarios | `admin.tarifarios` |
| **Gestión Financiera** | Seguros | `admin.seguros` |
| **Gestión Financiera** | Cuentas por Cobrar | `admin.cuentas` |
| **Gestión Operativa** | Gestionar Consulta Externa | `admin.consulta-externa-gestion` |
| **Gestión Operativa** | Almacén de Medicamentos | `admin.almacen-medicamentos.index` |
| **Gestión Operativa** | Control de Caja | `caja.gestion.index` |

### Dashboard Admin

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/dashboard` | `admin.dashboard` | GET | Dashboard principal administrador |

### Especialidades

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/especialidades` | `admin.especialidades.index` | GET | Listar especialidades |
| `/admin/especialidades/create` | `admin.especialidades.create` | GET | Crear especialidad |
| `/admin/especialidades` | `admin.especialidades.store` | POST | Guardar especialidad |
| `/admin/especialidades/{especialidad}/edit` | `admin.especialidades.edit` | GET | Editar especialidad |
| `/admin/especialidades/{especialidad}` | `admin.especialidades.update` | PUT | Actualizar especialidad |
| `/admin/especialidades/{especialidad}` | `admin.especialidades.destroy` | DELETE | Eliminar especialidad |

### Doctores

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/doctors` | `admin.doctors.index` | GET | Listar doctores |
| `/admin/doctors/create` | `admin.doctors.create` | GET | Crear doctor |
| `/admin/doctors` | `admin.doctors.store` | POST | Guardar doctor |
| `/admin/doctors/{doctor}/edit` | `admin.doctors.edit` | GET | Editar doctor |
| `/admin/doctors/{doctor}` | `admin.doctors.update` | PUT | Actualizar doctor |
| `/admin/doctors/{doctor}` | `admin.doctors.destroy` | DELETE | Eliminar doctor |
| `/admin/api/medicos-por-especialidad` | `admin.doctors.by-especialidad` | GET | API médicos por especialidad |

### Tarifarios (`admin|caja`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/tarifarios` | `admin.tarifarios` | GET | Listar tarifarios |
| `/admin/tarifarios` | `admin.tarifarios.store` | POST | Crear tarifa |
| `/admin/tarifarios/{tarifa}` | `admin.tarifarios.update` | PUT | Actualizar tarifa |
| `/admin/tarifarios/{tarifa}` | `admin.tarifarios.destroy` | DELETE | Eliminar tarifa |
| `/admin/api/tarifarios` | `admin.tarifarios.api.index` | GET | API tarifarios |
| `/admin/api/tarifarios/{tarifa}` | `admin.tarifarios.api.show` | GET | API ver tarifa |

### Seguros (`admin|caja`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/seguros` | `admin.seguros` | GET | Listar seguros médicos |
| `/admin/seguros` | `admin.seguros.store` | POST | Crear seguro |
| `/admin/seguros/{seguro}` | `admin.seguros.update` | PUT | Actualizar seguro |
| `/admin/seguros/{seguro}` | `admin.seguros.destroy` | DELETE | Eliminar seguro |
| `/admin/api/seguros` | `admin.seguros.api.index` | GET | API seguros |
| `/admin/api/seguros/{seguro}` | `admin.seguros.api.show` | GET | API ver seguro |
| `/admin/api/preautorizaciones` | `admin.seguros.api.preautorizaciones` | GET | API preautorizaciones |
| `/admin/api/preautorizaciones/{cuentaId}/estado` | `admin.seguros.api.cambiar-estado` | POST | Cambiar estado preautorización |

### Cuentas por Cobrar (`admin|caja`)

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/cuentas-por-cobrar` | `admin.cuentas` | GET | Listar cuentas por cobrar |
| `/admin/api/cuentas` | `admin.cuentas.api.index` | GET | API cuentas por cobrar |
| `/admin/api/cuentas/{id}` | `admin.cuentas.api.show` | GET | API ver cuenta |
| `/admin/api/cuentas/{id}/pago` | `admin.cuentas.api.pago` | POST | Registrar pago |
| `/admin/api/cuentas-emergencias` | `admin.cuentas.api.emergencias` | GET | API cuentas emergencias |
| `/admin/api/reporte-morosidad` | `admin.cuentas.api.morosidad` | GET | API reporte morosidad |

### Almacén de Medicamentos

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/almacen-medicamentos` | `admin.almacen-medicamentos.index` | GET | Almacén central de medicamentos |
| `/admin/almacen-medicamentos/create` | `admin.almacen-medicamentos.create` | GET | Crear medicamento |
| `/admin/almacen-medicamentos` | `admin.almacen-medicamentos.store` | POST | Guardar medicamento |
| `/admin/almacen-medicamentos/{almacenMedicamento}` | `admin.almacen-medicamentos.show` | GET | Ver medicamento |
| `/admin/almacen-medicamentos/{almacenMedicamento}/edit` | `admin.almacen-medicamentos.edit` | GET | Editar medicamento |
| `/admin/almacen-medicamentos/{almacenMedicamento}` | `admin.almacen-medicamentos.update` | PUT | Actualizar medicamento |
| `/admin/almacen-medicamentos/{almacenMedicamento}` | `admin.almacen-medicamentos.destroy` | DELETE | Eliminar medicamento |
| `/admin/almacen-medicamentos/{almacenMedicamento}/actualizar-stock` | `admin.almacen-medicamentos.actualizar-stock` | POST | Actualizar stock |
| `/admin/almacen-medicamentos/reporte/bajo-stock` | `admin.almacen-medicamentos.reporte.bajo-stock` | GET | Reporte de stock bajo |
| `/admin/almacen-medicamentos/reporte/vencimiento` | `admin.almacen-medicamentos.reporte.vencimiento` | GET | Reporte de vencimientos |
| `/admin/almacen-medicamentos/area/{area}` | `admin.almacen-medicamentos.por-area` | GET | Medicamentos por área |

### Emergencias Solo Lectura

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/admin/emergencies` | `admin.emergencies.index` | GET | Ver todas las emergencias (solo lectura) |
| `/admin/emergencies/{emergency}` | `admin.emergencies.show` | GET | Ver detalle de emergencia |
| `/admin/api/emergencias` | `admin.emergencies.api.index` | GET | API emergencias |
| `/admin/api/emergencias/{emergency}` | `admin.emergencies.api.show` | GET | API ver emergencia |

---

## 📈 10. ROL: GERENCIA (`gerente`)

**Middleware:** `role:admin|gerente`

**Prefix:** `/gerencial`  
**Name:** `gerencial.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/gerencial/dashboard` | `gerencial.dashboard` | GET | Dashboard gerencial |
| `/gerencial/reportes` | `gerencial.reportes` | GET | Reportes gerenciales |
| `/gerencial/kpis` | `gerencial.kpis` | GET | Indicadores KPI |

---

## 🔒 11. ROL: SEGURIDAD

**Middleware:** `role:admin|gerente|dirmedico`

**Prefix:** `/seguridad`  
**Name:** `seguridad.`

| Ruta | Nombre | Método | Descripción | Acceso |
|------|--------|--------|-------------|--------|
| `/seguridad/usuarios` | `seguridad.usuarios.index` | GET | Gestión de usuarios del sistema | admin, gerente, dirmedico |
| `/seguridad/usuarios/create` | `seguridad.usuarios.create` | GET | Crear usuario | admin, gerente |
| `/seguridad/usuarios` | `seguridad.usuarios.store` | POST | Guardar usuario | admin, gerente |
| `/seguridad/usuarios/{user}/edit` | `seguridad.usuarios.edit` | GET | Editar usuario | admin, gerente |
| `/seguridad/usuarios/{user}` | `seguridad.usuarios.update` | PUT | Actualizar usuario | admin, gerente |
| `/seguridad/usuarios/{user}` | `seguridad.usuarios.destroy` | DELETE | Eliminar usuario | admin, gerente |
| `/seguridad/auditoria` | `seguridad.auditoria.index` | GET | Auditoría del sistema | admin, gerente, dirmedico |
| `/seguridad/configuracion` | `seguridad.configuracion.index` | GET | Configuración del sistema | admin, gerente |
| `/seguridad/bitacora` | `seguridad.activity-logs.index` | GET | Bitácora de actividad | admin, gerente, dirmedico |

### Gestión de Usuarios (admin|gerente)

**Prefix:** `/user-management`  
**Name:** `user-management.`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/user-management/` | `user-management.index` | GET | Listar usuarios del sistema |
| `/user-management/create` | `user-management.create` | GET | Crear usuario |
| `/user-management/` | `user-management.store` | POST | Guardar usuario |
| `/user-management/{user}/edit` | `user-management.edit` | GET | Editar usuario |
| `/user-management/{user}` | `user-management.update` | PUT | Actualizar usuario |
| `/user-management/{user}` | `user-management.destroy` | DELETE | Eliminar usuario |
| `/user-management/{user}/toggle-status` | `user-management.toggle-status` | PATCH | Activar/Desactivar usuario |

---

## 🔐 AUTENTICACIÓN (Todos los roles)

**Middleware:** `auth`

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/` | - | GET | Redirección a login |
| `/dashboard` | `dashboard` | GET | Dashboard principal del usuario |
| `/profile` | `profile.edit` | GET | Editar perfil de usuario |
| `/profile` | `profile.update` | PATCH | Actualizar perfil |
| `/profile` | `profile.destroy` | DELETE | Eliminar cuenta |

---

## 📊 RESUMEN POR ROL

| Rol | Rutas Principales | Acceso |
|-----|-------------------|--------|
| **reception** | /reception, /patients, /admision, /reception/* | admin, reception, dirmedico |
| **emergencia** | /emergency-staff/*, /emergencias | emergencia, enfermera-emergencia, admin, dirmedico |
| **internacion** | /internacion-staff/* | internacion, enfermera-internacion, admin, dirmedico |
| **caja** | /caja-operativa/* | admin, caja |
| **farmacia** | /farmacia/* | admin, farmacia |
| **cirujano/quirofano** | /quirofano/* | admin, reception, dirmedico, cirujano |
| **uti** | /uti-operativo/*, /uti-admin/* | Según sub-rol: admin/dirmedico/doctor/enfermeria/uti |
| **doctor** | /consulta-externa, /medico/*, /uti, /hospitalizacion | admin, dirmedico, doctor |
| **admin** | /admin/*, /caja-gestion/*, /uti-admin/* | Solo admin |
| **gerente** | /gerencial/* | admin, gerente |
| **seguridad** | /seguridad/*, /user-management/* | admin, gerente, dirmedico |

---

## 📊 MENÚS DEL ROL ADMIN (Organización Master)

| Orden | Menú | Rol Principal | Descripción |
|-------|------|---------------|-------------|
| 10 | Dashboard | Todos | Panel principal |
| 20 | Pacientes | admin,dir_medico,doctor | Gestión de pacientes |
| 30 | Admin Emergencias | admin,dir_medico | Gestión de emergencias |
| 35 | Admin Internación | admin,dir_medico | Gestión de internación |
| 40 | Farmacia | farmacia | Operativa de farmacia |
| 50 | Administración | admin | **Master Menu** - Configuración general |
| 60 | Gerencial | admin,gerente | Reportes y KPIs |
| 70 | Seguridad | admin,gerente | Usuarios y auditoría |

### Estructura Master: Menú Administración

| Categoría | Submenús |
|-----------|----------|
| **Configuración General** | Especialidades, Doctores, Tarifarios, Seguros |
| **Gestión Hospitalaria** | Almacén Central, Farmacias, Medicamentos, Control de Caja |
| **Gestión Operativa** | Gestionar Consulta Externa, Facturación, Cuentas por Cobrar |

---

*Documento actualizado - Organizado por Rol*

---

## 📊 RESUMEN DE AGRUPACIÓN

| Módulo | Rutas | Roles | Observaciones |
|--------|-------|-------|---------------|
| Recepción | 25+ | admin, reception, dirmedico | Incluye 3 sub-módulos |
| Quirófano | 20+ | admin, reception, dirmedico | + gestión admin |
| Caja | 15+ | admin, caja | Dividido en operativa/gestión |
| Médico | 10+ | admin, dirmedico, doctor | |
| Emergencias | 20+ | admin, dirmedico, emergencia | + medicamentos |
| Internación | 15+ | admin, internacion, dirmedico | + habitaciones |
| UTI | 20+ | admin, uti, doctor | 3 sub-módulos |
| Farmacia | 15+ | admin, farmacia | |
| Administración | 30+ | admin (algunas con caja) | CRUDs principales |
| Gerencia | 3 | admin, gerente | Reportes |
| Seguridad | 10+ | admin, gerente, dirmedico | Usuarios + auditoría |

---

*Documento generado automáticamente a partir del análisis de `routes/web.php`*
