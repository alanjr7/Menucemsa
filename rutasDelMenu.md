# 📋 Informe de Rutas del Sistema Menucemsa

> **Archivo analizado:** `routes/web.php` (613 líneas)  
> **Fecha de generación:** Abril 2026

---

## 📊 Resumen General

| Métrica | Valor |
|---------|-------|
| **Total de rutas definidas** | ~150+ |
| **Roles identificados** | 10 roles |
| **Grupos principales** | 12 módulos |
| **Rutas comentadas/eliminables** | 15+ |

### Roles del Sistema

| Rol | Descripción |
|-----|-------------|
| `admin` | Administrador del sistema (acceso total) |
| `reception` | Personal de recepción |
| `dirmedico` | Director médico |
| `doctor` | Médicos |
| `caja` | Personal de caja/facturación |
| `farmacia` | Personal de farmacia |
| `gerente` | Gerencia |
| `emergencia` | Personal de emergencias |
| `internacion` | Personal de internación |
| `uti` | Personal de UTI |
| `enfermeria` | Personal de enfermería |

---

## 🗂️ Estructura por Módulos y Roles

### 1. 🔐 AUTENTICACIÓN Y PERFIL

**Middleware:** `auth`

| Ruta | Método | Controlador | Rol | Descripción |
|------|--------|-------------|-----|-------------|
| `/` | GET | Closure | Todos | Redirección a login |
| `/dashboard` | GET | DashboardController | auth, verified | Dashboard principal |
| `/profile` | GET | ProfileController | auth | Editar perfil |
| `/profile` | PATCH | ProfileController | auth | Actualizar perfil |
| `/profile` | DELETE | ProfileController | auth | Eliminar perfil |

**Ubicación en Menú:** 
- 📍 Disponible para todos los usuarios autenticados (menú superior)

---

### 2. 🏥 RECEPCIÓN Y PACIENTES

**Middleware:** `auth, role:admin|reception|dirmedico`

#### Rutas Principales

| Ruta | Nombre | Rol | Descripción |
|------|--------|-----|-------------|
| `/reception` | `reception` | admin, reception, dirmedico | Dashboard recepción |
| `/admision` | `admision.index` | admin, reception, dirmedico | Vista de admisión |
| `/patients` | `patients.index` | admin, reception, dirmedico | Lista de pacientes |
| `/patients/{ci}` | `patients.show` | admin, reception, dirmedico | Ver paciente |

#### Sub-módulos de Recepción

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/reception/consulta-externa` | `reception.consulta-externa` | Consulta externa |
| `/reception/emergencia` | `reception.emergencia` | Emergencias |
| `/reception/hospitalizacion` | `reception.hospitalizacion` | Hospitalización |

#### APIs Recepción

| Ruta | Método | Nombre | Propósito |
|------|--------|--------|-----------|
| `/api/buscar-paciente` | POST | `reception.buscar-paciente` | Buscar paciente por CI |
| `/api/registrar-consulta-externa` | POST | `reception.registrar-consulta` | Registrar consulta |
| `/api/triage-general` | POST | `reception.triage-general` | Procesar triage |
| `/api/emergency-ingreso` | POST | `reception.emergency-ingreso` | Ingreso emergencia |
| `/api/emergency-activas` | GET | `reception.emergency-activas` | Emergencias activas |
| `/api/registrar-emergencia` | POST | `reception.registrar-emergencia` | Registrar emergencia |
| `/api/registrar-hospitalizacion` | POST | `reception.registrar-hospitalizacion` | Registrar hospitalización |
| `/api/agenda-dia` | GET | `reception.agenda-dia` | Agenda del día |
| `/api/nueva-cita` | POST | `reception.nueva-cita` | Crear cita |
| `/api/estadisticas-dashboard` | GET | `reception.estadisticas` | Estadísticas |

**Ubicación en Menú:**
- 📍 **Menú Recepción** (visible para: admin, reception, dirmedico)
  - Recepción General
  - Consulta Externa
  - Emergencia
  - Hospitalización
  - Pacientes

---

### 3. 🔪 QUIRÓFANO

**Middleware:** `auth, role:admin|reception|dirmedico`

#### Rutas Principales

| Ruta | Nombre | Método | Descripción |
|------|--------|--------|-------------|
| `/quirofano` | `quirofano.index` | GET | Dashboard quirófano |
| `/quirofano/historial` | `quirofano.historial` | GET | Historial de cirugías |
| `/quirofano/create` | `quirofano.create` | GET | Crear programación |
| `/quirofano/calendario` | `quirofano.calendario` | GET | Vista calendario |
| `/quirofano` | `quirofano.store` | POST | Guardar programación |
| `/quirofano/{cita}` | `quirofano.show` | GET | Ver cirugía |
| `/quirofano/{cita}/edit` | `quirofano.edit` | GET | Editar cirugía |
| `/quirofano/{cita}` | `quirofano.update` | PUT | Actualizar cirugía |
| `/quirofano/{cita}/iniciar` | `quirofano.iniciar` | POST | Iniciar cirugía |
| `/quirofano/{cita}/finalizar` | `quirofano.finalizar` | POST | Finalizar cirugía |
| `/quirofano/{cita}/cancelar` | `quirofano.cancelar` | POST | Cancelar cirugía |

#### APIs Quirófano

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/quirofano/disponibilidad` | `quirofano.disponibilidad` | Verificar disponibilidad |
| `/api/quirofanos-disponibles` | `api.quirofanos-disponibles` | Listar quirófanos |
| `/api/paciente/{ci}` | `api.paciente` | Datos del paciente |
| `/api/medico/{ci}` | `api.medico` | Datos del médico |
| `/api/pacientes-lista` | `api.pacientes-lista` | Lista de pacientes |
| `/api/medicos-lista` | `api.medicos-lista` | Lista de médicos |

#### Gestión de Quirófanos (SOLO ADMIN)

**Middleware adicional:** `role:admin`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/quirofanos-management` | `quirofanos.management.index` | Gestionar quirófanos |
| `/quirofanos-management/create` | `quirofanos.management.create` | Crear quirófano |
| `/quirofanos-management/{quirofano}/edit` | `quirofanos.management.edit` | Editar quirófano |
| `/api/quirofanos/next-number` | `quirofanos.api.next-number` | Siguiente número |

#### Medicamentos de Quirófano (SOLO ADMIN)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/quirofano/medicamentos` | `quirofano.medicamentos.index` | Listar medicamentos |
| `/quirofano/medicamentos/create` | `quirofano.medicamentos.create` | Crear medicamento |
| `/quirofano/medicamentos/{medicamento}` | `quirofano.medicamentos.show` | Ver medicamento |
| `/quirofano/medicamentos/{medicamento}/stock` | `quirofano.medicamentos.stock` | Actualizar stock |

**Ubicación en Menú:**
- 📍 **Menú Quirófano** (visible para: admin, reception, dirmedico)
  - Programar Cirugía
  - Calendario
  - Historial
  - Gestión de Quirófanos (solo admin)
  - Medicamentos (solo admin)

---

### 4. 💰 CAJA / FACTURACIÓN

#### 4.1 Caja Operativa (Rol: admin|caja)

**Prefix:** `/caja-operativa`  
**Name:** `caja.operativa.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/caja-operativa/` | `caja.operativa.index` | Dashboard caja |
| `/caja-operativa/abrir` | `caja.operativa.abrir` | Abrir caja |
| `/caja-operativa/cerrar` | `caja.operativa.cerrar` | Cerrar caja |
| `/caja-operativa/pacientes-pendientes` | `caja.operativa.pacientes-pendientes` | Pacientes por cobrar |
| `/caja-operativa/detalle-cuenta/{id}` | `caja.operativa.detalle-cuenta` | Detalle de cuenta |
| `/caja-operativa/procesar-cobro` | `caja.operativa.procesar-cobro` | Procesar cobro |
| `/caja-operativa/uti-pacientes` | `caja.operativa.uti-pacientes` | Pacientes UTI |
| `/caja-operativa/uti-procesar-cobro/{id}` | `caja.operativa.uti-procesar-cobro` | Cobro UTI |

#### 4.2 Gestión de Caja (SOLO ADMIN)

**Prefix:** `/caja-gestion`  
**Name:** `caja.gestion.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/caja-gestion/` | `caja.gestion.index` | Gestión de cajas |
| `/caja-gestion/transacciones` | `caja.gestion.transacciones` | Historial transacciones |
| `/caja-gestion/control-cajas` | `caja.gestion.control-cajas` | Control de cajas |
| `/caja-gestion/resumen-financiero` | `caja.gestion.resumen-financiero` | Reportes financieros |
| `/caja-gestion/auditoria` | `caja.gestion.auditoria` | Auditoría |

**Ubicación en Menú:**
- 📍 **Menú Caja** (visible para: admin, caja)
  - Caja Operativa
  - Gestión de Caja (solo admin)

---

### 5. 🩺 ÁREA MÉDICA

**Middleware:** `role:admin|dirmedico|doctor`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/enfermeria` | `enfermeria.index` | Dashboard enfermería |
| `/uti` | `uti.index` | Dashboard UTI médico |
| `/hospitalizacion` | `hospitalizacion.index` | Dashboard hospitalización |
| `/medico/dashboard` | `medico.dashboard` | Dashboard del médico |
| `/medico/atender-paciente` | `medico.atender-paciente` | Atender paciente |

#### Consulta Externa (doctores)

**Middleware:** `role:doctor|dirmedico|admin`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/consulta-externa` | `consulta.index` | Lista de consultas |
| `/consulta/{consultaCodigo}` | `consulta.ver` | Ver consulta |
| `/consulta-externa/iniciar/{consultaId}` | `consulta.iniciar` | Iniciar consulta |
| `/consulta-externa/completar/{consultaId}` | `consulta.completar` | Completar consulta |
| `/api/paciente/{ci}` | `consulta.paciente` | API datos paciente |

**Ubicación en Menú:**
- 📍 **Menú Médico** (visible para: admin, dirmedico, doctor)
  - Mi Dashboard
  - Consulta Externa
  - Hospitalización
  - UTI
  - Enfermería

---

### 6. 🚑 EMERGENCIAS

#### 6.1 Vista General (admin|dirmedico|emergencia)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/emergencias` | `emergencias.index` | Dashboard emergencias |

#### 6.2 Staff de Emergencias (emergencia|admin|dirmedico)

**Prefix:** `/emergency-staff`  
**Name:** `emergency-staff.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/emergency-staff/dashboard` | `emergency-staff.dashboard` | Dashboard staff |
| `/emergency-staff/create` | `emergency-staff.create` | Crear emergencia |
| `/emergency-staff/pending` | `emergency-staff.pending` | Emergencias pendientes |
| `/emergency-staff/{emergency}` | `emergency-staff.show` | Ver emergencia |
| `/emergency-staff/{emergency}/evaluacion` | `emergency-staff.evaluacion` | Evaluar paciente |
| `/emergency-staff/{emergency}/historial` | `emergency-staff.historial` | Historial |

#### 6.3 Medicamentos de Emergencia (admin|emergencia)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/emergency-staff/medicamentos` | `emergency-staff.medicamentos.index` | Inventario |
| `/emergency-staff/medicamentos/create` | `emergency-staff.medicamentos.create` | Agregar medicamento |
| `/emergency-staff/medicamentos/{medicamento}` | `emergency-staff.medicamentos.show` | Ver medicamento |

**Ubicación en Menú:**
- 📍 **Menú Emergencias** (visible para: admin, dirmedico, emergencia)
  - Dashboard Emergencias
  - Pacientes en Espera
  - Medicamentos

---

### 7. 🏨 INTERNACIÓN / HOSPITALIZACIÓN

**Middleware:** `role:internacion|admin|dirmedico`

**Prefix:** `/internacion-staff`  
**Name:** `internacion-staff.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/internacion-staff/dashboard` | `internacion-staff.dashboard` | Dashboard internación |
| `/internacion-staff/habitaciones` | `internacion-staff.habitaciones.index` | Gestión de habitaciones |
| `/internacion-staff/habitaciones/create` | `internacion-staff.habitaciones.create` | Crear habitación |
| `/internacion-staff/habitaciones/{habitacion}` | `internacion-staff.habitaciones.show` | Ver habitación |
| `/internacion-staff/camas/{cama}/liberar` | `internacion-staff.camas.liberar` | Liberar cama |

#### Medicamentos de Internación (admin|internacion)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/internacion-staff/medicamentos` | `internacion-staff.medicamentos.index` | Inventario |
| `/internacion-staff/medicamentos/create` | `internacion-staff.medicamentos.create` | Agregar medicamento |

**Ubicación en Menú:**
- 📍 **Menú Internación** (visible para: admin, internacion, dirmedico)
  - Dashboard
  - Habitaciones
  - Medicamentos

---

### 8. 🧪 UTI (TERAPIA INTENSIVA)

#### 8.1 UTI Operativo (admin|dirmedico|doctor|enfermeria|uti)

**Prefix:** `/uti-operativo`  
**Name:** `uti.operativa.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/uti-operativo/` | `uti.operativa.index` | Dashboard UTI |
| `/uti-operativo/paciente/{id}` | `uti.operativa.paciente.show` | Ficha del paciente |
| `/uti-operativo/medicamentos` | `uti.operativa.medicamentos.index` | Medicamentos (admin|uti) |

#### 8.2 UTI Administración (SOLO ADMIN)

**Prefix:** `/uti-admin`  
**Name:** `uti.admin.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/uti-admin/` | `uti.admin.index` | Admin UTI |
| `/uti-admin/camas` | `uti.admin.camas` | Gestión de camas |
| `/uti-admin/control-financiero` | `uti.admin.control-financiero` | Control financiero |
| `/uti-admin/tarifario` | `uti.admin.tarifario` | Tarifario UTI |

#### 8.3 UTI Recepción (admin|reception|dirmedico)

**Prefix:** `/reception/uti`  
**Name:** `reception.uti.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/reception/uti/ingreso` | `reception.uti.ingreso` | Ingreso a UTI |

**Ubicación en Menú:**
- 📍 **Menú UTI** (visible según rol)
  - **Operativo:** admin, dirmedico, doctor, enfermeria, uti
  - **Administración:** solo admin
  - **Ingreso:** admin, reception, dirmedico

---

### 9. 💊 FARMACIA

**Middleware:** `auth, role:admin|farmacia`

**Prefix:** `/farmacia`  
**Name:** `farmacia.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/farmacia/` | `farmacia.index` | Dashboard farmacia |
| `/farmacia/punto-de-venta` | `farmacia.pos` | Punto de venta |
| `/farmacia/inventario` | `farmacia.inventario` | Inventario |
| `/farmacia/ventas` | `farmacia.ventas` | Historial de ventas |
| `/farmacia/clientes` | `farmacia.clientes` | Gestión de clientes |
| `/farmacia/reporte` | `farmacia.reporte` | Reportes |

**Ubicación en Menú:**
- 📍 **Menú Farmacia** (visible para: admin, farmacia)
  - Punto de Venta
  - Inventario
  - Ventas
  - Clientes
  - Reportes

---

### 10. ⚙️ ADMINISTRACIÓN

#### 10.1 Admin - Especialidades (SOLO ADMIN)

**Prefix:** `/admin`  
**Name:** `admin.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/dashboard` | `admin.dashboard` | Dashboard admin |
| `/admin/especialidades` | `admin.especialidades.index` | Especialidades |
| `/admin/especialidades/create` | `admin.especialidades.create` | Crear especialidad |
| `/admin/especialidades/{especialidad}/edit` | `admin.especialidades.edit` | Editar especialidad |

#### 10.2 Admin - Doctores (SOLO ADMIN)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/doctors` | `admin.doctors.index` | Lista de doctores |
| `/admin/doctors/create` | `admin.doctors.create` | Crear doctor |
| `/admin/doctors/{doctor}/edit` | `admin.doctors.edit` | Editar doctor |
| `/admin/api/medicos-por-especialidad` | `admin.doctors.by-especialidad` | API médicos |

#### 10.3 Admin - Tarifarios (admin|caja)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/tarifarios` | `admin.tarifarios` | Tarifarios |
| `/admin/api/tarifarios` | `admin.tarifarios.api.index` | API tarifas |

#### 10.4 Admin - Seguros (admin|caja)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/seguros` | `admin.seguros` | Seguros médicos |
| `/admin/api/seguros` | `admin.seguros.api.index` | API seguros |
| `/admin/api/preautorizaciones` | `admin.seguros.api.preautorizaciones` | Preautorizaciones |

#### 10.5 Admin - Cuentas por Cobrar (admin|caja)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/cuentas-por-cobrar` | `admin.cuentas` | Cuentas por cobrar |
| `/admin/api/cuentas` | `admin.cuentas.api.index` | API cuentas |
| `/admin/api/reporte-morosidad` | `admin.cuentas.api.morosidad` | Reporte morosidad |

#### 10.6 Admin - Almacén de Medicamentos (SOLO ADMIN)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/almacen-medicamentos` | `admin.almacen-medicamentos.index` | Almacén |
| `/admin/almacen-medicamentos/create` | `admin.almacen-medicamentos.create` | Agregar medicamento |
| `/admin/almacen-medicamentos/reporte/bajo-stock` | `admin.almacen-medicamentos.reporte.bajo-stock` | Reporte stock |
| `/admin/almacen-medicamentos/reporte/vencimiento` | `admin.almacen-medicamentos.reporte.vencimiento` | Reporte vencimientos |

#### 10.7 Admin - Emergencias Solo Lectura (SOLO ADMIN)

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/admin/emergencies` | `admin.emergencies.index` | Ver emergencias |
| `/admin/api/emergencias` | `admin.emergencies.api.index` | API emergencias |

**Ubicación en Menú:**
- 📍 **Menú Administración** (visible para: admin)
  - Dashboard
  - Especialidades
  - Doctores
  - Tarifarios
  - Seguros
  - Cuentas por Cobrar
  - Almacén de Medicamentos
  - Emergencias (solo lectura)

---

### 11. 📈 GERENCIA

**Middleware:** `role:admin|gerente`

**Prefix:** `/gerencial`  
**Name:** `gerencial.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/gerencial/dashboard` | `gerencial.dashboard` | Dashboard gerencia |
| `/gerencial/reportes` | `gerencial.reportes` | Reportes |
| `/gerencial/kpis` | `gerencial.kpis` | Indicadores KPI |

**Ubicación en Menú:**
- 📍 **Menú Gerencia** (visible para: admin, gerente)
  - Dashboard
  - Reportes
  - KPIs

---

### 12. 🔒 SEGURIDAD

**Middleware:** `role:admin|gerente|dirmedico`

**Prefix:** `/seguridad`  
**Name:** `seguridad.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/seguridad/usuarios` | `seguridad.usuarios.index` | Gestión de usuarios |
| `/seguridad/usuarios/create` | `seguridad.usuarios.create` | Crear usuario |
| `/seguridad/usuarios/{user}/edit` | `seguridad.usuarios.edit` | Editar usuario |
| `/seguridad/auditoria` | `seguridad.auditoria.index` | Auditoría |
| `/seguridad/configuracion` | `seguridad.configuracion.index` | Configuración |
| `/seguridad/bitacora` | `seguridad.activity-logs.index` | Bitácora de actividad |

#### Gestión de Usuarios (admin|gerente)

**Prefix:** `/user-management`  
**Name:** `user-management.`

| Ruta | Nombre | Descripción |
|------|--------|-------------|
| `/user-management/` | `user-management.index` | Usuarios del sistema |
| `/user-management/create` | `user-management.create` | Crear usuario |
| `/user-management/{user}/edit` | `user-management.edit` | Editar usuario |
| `/user-management/{user}/toggle-status` | `user-management.toggle-status` | Activar/Desactivar |

**Ubicación en Menú:**
- 📍 **Menú Seguridad** (visible para: admin, gerente, dirmedico)
  - Usuarios
  - Auditoría
  - Configuración
  - Bitácora

---

## ⚠️ RUTAS COMENTADAS / DEPRECADAS / ELIMINABLES

Las siguientes rutas están comentadas o son obsoletas y podrían eliminarse para limpiar el código:

### Rutas Comentadas (Líneas 219-221, 240-252, 266-270)

| Línea | Ruta | Motivo |
|-------|------|--------|
| 219-221 | `/caja` (antiguo) | Reemplazado por `/caja-operativa` |
| 240-242 | `/test-doctor` | Ruta de prueba |
| 245-252 | `/test-doctor-class` | Ruta de prueba |
| 266-270 | `/doctor/*` | Comentadas - duplicadas |

### Rutas de Prueba (Eliminables en producción)

| Ruta | Línea | Descripción |
|------|-------|-------------|
| `/test-emergency-access` | 507-523 | Diagnóstico de acceso |
| `/test-role-middleware` | 526-528 | Test de middleware |
| `/test-farmacia` | 531-538 | Test de farmacia |

### Rutas Duplicadas o Redundantes

1. **API de emergencias temporales** - Duplicidad entre `/api/emergencias-temporales` y rutas en emergency-staff
2. **Completar datos paciente** - Rutas duplicadas en `/api/` y `/reception/`

---

## 📋 MAPA DE MENÚ POR ROL

### 🔴 ADMIN (Acceso Total)

```
📊 Dashboard
├── Dashboard Principal
├── Dashboard Admin
└── Dashboard Gerencial

🏥 Recepción
├── Recepción General
├── Consulta Externa
├── Emergencia
├── Hospitalización
└── Pacientes

🔪 Quirófano
├── Programar Cirugía
├── Calendario
├── Historial
├── Gestión de Quirófanos
└── Medicamentos Quirófano

💰 Caja
├── Caja Operativa
└── Gestión de Caja

🩺 Área Médica
├── Mi Dashboard
├── Consulta Externa
├── Hospitalización
├── UTI
└── Enfermería

🚑 Emergencias
├── Dashboard Emergencias
├── Staff de Emergencias
└── Medicamentos Emergencia

🏨 Internación
├── Dashboard Internación
├── Habitaciones
└── Medicamentos Internación

🧪 UTI
├── UTI Operativo
├── UTI Administración
└── UTI Recepción (compartido)

💊 Farmacia
├── Punto de Venta
├── Inventario
├── Ventas
├── Clientes
└── Reportes

⚙️ Administración
├── Especialidades
├── Doctores
├── Tarifarios
├── Seguros
├── Cuentas por Cobrar
├── Almacén de Medicamentos
└── Emergencias (solo lectura)

📈 Gerencia
├── Reportes
└── KPIs

🔒 Seguridad
├── Usuarios
├── Auditoría
├── Configuración
└── Bitácora
```

### 🟡 RECEPTION

```
🏥 Recepción
├── Recepción General
├── Consulta Externa
├── Emergencia
├── Hospitalización
└── Pacientes

🔪 Quirófano (solo ver/programar)
├── Programar Cirugía
├── Calendario
└── Historial

🧪 UTI Recepción
└── Ingreso UTI
```

### 🟢 DIRMEDICO (Director Médico)

```
🏥 Recepción
├── Recepción General
├── Consulta Externa
├── Emergencia
├── Hospitalización
└── Pacientes

🔪 Quirófano
├── Programar Cirugía
├── Calendario
└── Historial

🩺 Área Médica
├── Mi Dashboard
├── Consulta Externa
├── Hospitalización
├── UTI
└── Enfermería

🚑 Emergencias
└── Dashboard Emergencias

🏨 Internación
└── Dashboard Internación

🧪 UTI
└── UTI Operativo

🔒 Seguridad
└── Usuarios (solo ver)
```

### 🔵 DOCTOR

```
🩺 Área Médica
├── Mi Dashboard
├── Consulta Externa
├── Hospitalización
└── UTI
```

### 🟣 CAJA

```
💰 Caja
└── Caja Operativa

⚙️ Administración (limitado)
├── Tarifarios (ver)
├── Seguros (ver)
└── Cuentas por Cobrar (ver)
```

### 🟠 FARMACIA

```
💊 Farmacia
├── Punto de Venta
├── Inventario
├── Ventas
├── Clientes
└── Reportes
```

### 🟤 EMERGENCIA (Staff)

```
🚑 Emergencias
├── Dashboard Emergencias
├── Staff de Emergencias
│   ├── Dashboard
│   ├── Crear Emergencia
│   ├── Pendientes
│   └── Evaluación
└── Medicamentos Emergencia
```

### ⚪ INTERNACIÓN (Staff)

```
🏨 Internación
├── Dashboard Internación
├── Habitaciones
└── Medicamentos Internación
```

### 🟦 UTI (Staff)

```
🧪 UTI
└── UTI Operativo
    ├── Dashboard
    ├── Ficha Paciente
    └── Medicamentos (solo admin/uti)
```

### 🟫 GERENTE

```
📊 Dashboard
└── Dashboard Gerencial

📈 Gerencia
├── Reportes
└── KPIs

🔒 Seguridad
└── Usuarios
```

---

## 🧹 RECOMENDACIONES DE LIMPIEZA

### 1. Eliminar rutas de prueba
```php
// ELIMINAR líneas 507-538
/test-emergency-access
/test-role-middleware
/test-farmacia
```

### 2. Consolidar rutas duplicadas
- Unificar las APIs de emergencias entre `/api/` y `/emergency-staff/api/`
- Consolidar rutas de completar datos de paciente

### 3. Eliminar código comentado obsoleto
```php
// Líneas 219-221 - Sistema antiguo de caja
// Líneas 240-252 - Rutas de test
// Líneas 266-270 - Rutas doctor comentadas
```

### 4. Agrupar rutas API
Considerar mover todas las rutas API a un archivo separado `routes/api.php` para mejor organización.

### 5. Estandarizar nombres
Algunas rutas usan nombres inconsistentes:
- `admin.especialidades.index` vs `admin.tarifarios` (sin .index)
- `emergency-staff.` vs `emergencias.`

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
| UTI | 20+ | admin, uti, doctor, enfermeria | 3 sub-módulos |
| Farmacia | 15+ | admin, farmacia | |
| Administración | 30+ | admin (algunas con caja) | CRUDs principales |
| Gerencia | 3 | admin, gerente | Reportes |
| Seguridad | 10+ | admin, gerente, dirmedico | Usuarios + auditoría |

---

*Documento generado automáticamente a partir del análisis de `routes/web.php`*
