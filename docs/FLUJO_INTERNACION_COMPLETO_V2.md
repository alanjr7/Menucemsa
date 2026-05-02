# Flujo Completo de Internación con Traslados entre Áreas

## Resumen Ejecutivo

Este documento describe el flujo completo de internación de un paciente, incluyendo **todos los traslados y derivaciones** entre áreas: Emergencia, Hospitalización, UTI y Quirófano. El sistema implementa un modelo de **Cuenta Maestra** que unifica todos los servicios bajo una única cuenta por paciente, independientemente de las áreas por las que transite.

---

## Diagramas Actualizados

| Archivo | Descripción |
|---------|-------------|
| `docs/diagramas/flujo-internacion-plantuml-v2.puml` | Diagrama de actividades con traslados |
| `docs/diagramas/flujo-internacion-secuencia-v2.puml` | Diagrama de secuencia con traslados |
| `docs/diagramas/flujo-internacion-estados-v2.puml` | Diagrama de estados con traslados |

---

## Tabla de Traslados entre Áreas

| Origen | Destino | Método | Controller | Línea | Descripción |
|--------|---------|--------|------------|-------|-------------|
| **Hospitalización** | **UTI** | `derivarAUti()` | `InternacionStaffController` | 142 | Deriva paciente de internación a UTI |
| **Hospitalización** | **Quirófano** | `derivarAQuirofano()` | `InternacionStaffController` | 232 | Deriva paciente a cirugía |
| **UTI** | **Hospitalización** | `trasladarPaciente()` | `UtiOperativoController` | 607 | Traslada paciente estable a hospitalización |
| **UTI** | **Quirófano** | `trasladarPaciente()` | `UtiOperativoController` | 607 | Traslada paciente a cirugía |
| **UTI** | **Domicilio** | `darAltaClinica()` | `UtiOperativoController` | 564 | Alta de UTI a domicilio |
| **UTI** | **Otro Hospital** | `darAltaClinica()` | `UtiOperativoController` | 564 | Alta de UTI a otro centro médico |
| **Emergencia** | **Hospitalización** | `derivar()` | `EmergencyStaffController` | 129 | Deriva paciente de emergencia a internación |
| **Emergencia** | **Cirugía** | `derivar()` | `EmergencyStaffController` | 156 | Deriva paciente de emergencia a quirófano |

---

## Fases del Flujo con Traslados

### FASE 1: Ingreso y Registro (Recepción)

**Archivos principales:**
- `app/Http/Controllers/Reception/HospitalizacionController.php`
- `app/Services/CuentaCobroService.php`

**Flujo:**
1. Paciente llega a recepción
2. Recepcionista busca paciente por CI (mínimo 3 dígitos)
3. Se verifica que no tenga hospitalización activa
4. Se crea triage amarillo
5. Se genera ID: `HOSP-{YmdHis}-{random(100,999)}`

---

### FASE 2: Cuenta Maestra (Unificación de Servicios)

**Regla fundamental:** `UN paciente = UNA cuenta activa a la vez`

Esta regla aplica incluso cuando el paciente se traslada entre áreas:
- Si existe cuenta pendiente/parcial → Se reutiliza
- Si no existe → Se crea nueva con episodio_numero incrementado
- Todos los cargos de todas las áreas van a la misma cuenta

**Archivo:** `app/Services/CuentaCobroService.php:32-90`

---

### FASE 3: Asignación de Habitación

**Archivo:** `app/Services/Habitacion/AsignacionCamaService.php`

1. Verificar disponibilidad de cama
2. Crear detalle de estadía (tipo: `estadia`)
3. Actualizar hospitalización con habitacion_id, cama_id
4. Marcar cama como `ocupada`

---

### FASE 4: Atención Médica

**Archivo:** `app/Http/Controllers/Medical/HospitalizacionController.php`

- Evoluciones diarias
- Medicamentos (descuenta inventario)
- Equipos médicos con precio personalizado
- Historial en JSON (`equipos_medicos`)

---

### FASE 5: TRASLADOS Y DERIVACIONES

#### 5.1 Traslado: Hospitalización → UTI

**Archivo:** `app/Http/Controllers/InternacionStaffController.php:142-227`

```php
public function derivarAUti(Request $request, $id): JsonResponse
```

**Flujo:**
1. Médico solicita derivación a UTI
2. Sistema verifica cama UTI disponible (`uti_beds.status = 'disponible'`)
3. Genera Nro. Ingreso UTI: `UTI-{Y}-{m}-{secuencia}`
4. Crea `UtiAdmission` vinculada:
   - `hospitalization_id` = ID de hospitalización origen
   - `tipo_ingreso` = 'derivacion_interna'
   - `estado` = 'activo'
5. Marca cama UTI como ocupada
6. Actualiza hospitalización:
   - `estado` = 'trasladado'
   - Agrega observación: "Trasladado a UTI: {fecha_hora}"
7. Personal UTI recibe paciente

**Cuenta de Cobro:**
- Se mantiene la misma cuenta maestra
- Se agregan cargos de UTI a la cuenta existente

---

#### 5.2 Traslado: Hospitalización → Quirófano

**Archivo:** `app/Http/Controllers/InternacionStaffController.php:232-313`

```php
public function derivarAQuirofano(Request $request, $id): JsonResponse
```

**Flujo:**
1. Médico solicita derivación a quirófano
2. Sistema verifica quirófano disponible
3. Genera Nro. Cirugía: `CIR-{Ymd}-{id}`
4. Crea registro en `Emergency`:
   - `status` = 'cirugia'
   - `ubicacion_actual` = 'cirugia'
   - `nro_cirugia` = CIR-xxx
   - `flujo_historial` = [desde:'internacion', hasta:'cirugia']
5. Actualiza hospitalización:
   - `estado` = 'trasladado'
   - Agrega observación de traslado
6. Cirujano recibe paciente en quirófano

---

#### 5.3 Derivación: Emergencia → Hospitalización

**Archivo:** `app/Http/Controllers/EmergencyStaffController.php:129-228`

```php
public function derivar(Request $request, Emergency $emergency): JsonResponse
```

**Flujo:**
1. Personal de emergencia evalúa paciente
2. Determina necesidad de hospitalización
3. Valida recursos: `validarRecursos('hospitalizacion')`
4. Genera Nro. Hospitalización: `HOSP-{Ymd}-{id}`
5. Crea `Hospitalizacion`:
   - `nro_emergencia` = ID de emergencia origen
   - `estado` = 'activo'
6. **Vincula a cuenta maestra**:
   - Reutiliza cuenta de emergencia si existe
   - Unifica todos los cargos en una sola cuenta
7. Actualiza `Emergency`:
   - `status` = 'hospitalizacion'
   - `ubicacion_actual` = 'hospitalizacion'
   - `nro_hospitalizacion` = HOSP-xxx
8. Registra movimiento en `flujo_historial`

---

#### 5.4 Derivación: Emergencia → Cirugía

**Archivo:** `app/Http/Controllers/EmergencyStaffController.php:156-162`

**Flujo:**
1. Personal de emergencia determina necesidad de cirugía
2. Valida recursos: `validarRecursos('cirugia')`
3. Actualiza `Emergency`:
   - `status` = 'cirugia'
   - `ubicacion_actual` = 'cirugia'
   - Genera `nro_cirugia`
4. Cirujano recibe paciente

---

#### 5.5 Traslado: UTI → Hospitalización

**Archivo:** `app/Http/Controllers/Medical/UtiOperativoController.php:607-631`

```php
public function trasladarPaciente(Request $request, $admissionId): JsonResponse
```

**Flujo:**
1. Personal UTI determina paciente estable
2. Libera cama UTI:
   - `uti_beds.status` = 'disponible'
3. Actualiza `UtiAdmission`:
   - `estado` = 'trasladado'
   - Agrega observación del traslado
4. Paciente puede:
   - Volver a hospitalización previa
   - Crear nueva hospitalización

---

#### 5.6 Traslado: UTI → Quirófano

**Mismo método:** `trasladarPaciente()`

**Flujo:**
1. Paciente en UTI requiere cirugía urgente
2. Libera cama UTI temporalmente
3. Marca estado como 'trasladado'
4. Cirujano realiza procedimiento
5. Paciente puede volver a UTI u otra área

---

#### 5.7 Alta desde UTI

**Archivo:** `app/Http/Controllers/Medical/UtiOperativoController.php:564-602`

```php
public function darAltaClinica(Request $request, $admissionId): JsonResponse
```

**Requisitos para alta UTI:**
- Día validado (`dia_validado = true`)
- Signos vitales registrados
- Ronda médica completada

**Destinos posibles:**
- `hospitalizacion` → Continúa en hospitalización
- `domicilio` → Alta a casa
- `otro_hospital` → Derivación a otro centro

---

### FASE 6: Alta Médica y Liberación

**Archivos:**
- `app/Http/Controllers/Reception/HospitalizacionController.php:319-349`
- `app/Services/Habitacion/LiberacionCamaService.php`

**Flujo:**
1. Médico solicita alta médica
2. Sistema calcula estadía: `max(1, diffInDays + 1)`
3. Calcula costo total: `días × precio_cama_dia`
4. Actualiza detalle de estadía en cuenta
5. Libera cama
6. Marca `fecha_alta` en hospitalización

---

### FASE 7: Cobro en Caja

**Archivo:** `app/Http/Controllers/Caja/CajaOperativaController.php:426-600`

**Flujo:**
1. Cajero abre caja (CajaSession)
2. Busca cuenta del paciente
3. Aplica seguro automáticamente si corresponde
4. Calcula saldo considerando:
   - Cargos de todas las áreas visitadas
   - Cobertura del seguro
   - Pagos previos
5. Procesa pago (efectivo, transferencia, tarjeta, QR)
6. Genera comprobante

---

## Estados de Traslado

### Estados de Hospitalización

| Estado | Descripción | Transiciones posibles |
|--------|-------------|----------------------|
| `activo` | Atención en curso | → trasladado, → alta |
| `trasladado` | Derivado a otra área | (fin en internación) |
| `alta` | Dado de alta médica | → cobro → pagado |

### Estados de UTI

| Estado | Descripción | Transiciones posibles |
|--------|-------------|----------------------|
| `activo` | Atención intensiva en curso | → trasladado, → alta_clinica |
| `trasladado` | Trasladado a otra área | (fin en UTI) |
| `alta_clinica` | Alta clínica registrada | → cobro → pagado |

### Estados de Emergencia

| Estado | Descripción | Transiciones posibles |
|--------|-------------|----------------------|
| `recibido` | Paciente en espera | → en_evaluacion |
| `en_evaluacion` | En atención médica | → hospitalizacion, → cirugia, → alta |
| `hospitalizacion` | Derivado a internación | (fin en emergencia) |
| `cirugia` | En quirófano | (fin en emergencia) |

---

## Vinculación entre Áreas

### Vínculos de Datos

```
┌─────────────────────────────────────────────────────────────┐
│                    VÍNCULOS ENTRE ÁREAS                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Hospitalización ──────┬──────> UtiAdmission               │
│     (HOSP-xxx)         │          hospitalization_id        │
│                        │                                      │
│                        └──────> Emergency                    │
│                                   nro_hospitalizacion        │
│                                                             │
│  Emergencia ───────────> Hospitalizacion                   │
│     (EMG-xxx)              nro_emergencia                  │
│                                                             │
│  UTI ──────────────────> Hospitalizacion (alta)            │
│     (UTI-xxx)            destino_alta = 'hospitalizacion'  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Campos de Vinculación

| Área Origen | Área Destino | Campo de Vinculación | Valor |
|-------------|--------------|---------------------|-------|
| Hospitalización | UTI | `uti_admissions.hospitalization_id` | ID HOSP-xxx |
| Emergencia | Hospitalización | `hospitalizaciones.nro_emergencia` | ID EMG-xxx |
| Emergencia | Hospitalización | `emergencies.nro_hospitalizacion` | ID HOSP-xxx |
| UTI | Hospitalización | `uti_admissions.destino_alta` | 'hospitalizacion' |

---

## Cuenta Maestra en Traslados

### Regla de Unificación

La cuenta maestra se **reutiliza** cuando un paciente se traslada entre áreas:

```php
// Ejemplo: Derivación Emergencia → Hospitalización
$cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra(
    $ciPaciente,
    'internacion'  // Tipo de atención actual
);
// Si existe cuenta de emergencia, la reutiliza
```

### Tipos de Atención en Cuenta

| Tipo | Descripción |
|------|-------------|
| `internacion` | Hospitalización general |
| `emergencia` | Servicio de emergencias |
| `uti` | Unidad de Terapia Intensiva |
| `cirugia` | Procedimientos quirúrgicos |

**Nota:** Aunque el `tipo_atencion` se establece al crear la cuenta, todos los cargos de todas las áreas se acumulan en la misma cuenta maestra.

---

## Archivos Clave por Traslado

### Traslados desde Hospitalización

| Método | Archivo | Líneas |
|--------|---------|--------|
| `derivarAUti()` | `InternacionStaffController.php` | 142-227 |
| `derivarAQuirofano()` | `InternacionStaffController.php` | 232-313 |

### Traslados desde Emergencia

| Método | Archivo | Líneas |
|--------|---------|--------|
| `derivar()` | `EmergencyStaffController.php` | 129-228 |
| `validarRecursos()` | `EmergencyStaffController.php` | 263-300 |

### Traslados desde UTI

| Método | Archivo | Líneas |
|--------|---------|--------|
| `trasladarPaciente()` | `UtiOperativoController.php` | 607-631 |
| `darAltaClinica()` | `UtiOperativoController.php` | 564-602 |

---

## Rutas API para Traslados

| Método | Ruta | Descripción | Controller |
|--------|------|-------------|------------|
| POST | `/api/internacion/{id}/derivar-uti` | Derivar a UTI | InternacionStaffController |
| POST | `/api/internacion/{id}/derivar-quirofano` | Derivar a quirófano | InternacionStaffController |
| POST | `/api/emergencias/{emergency}/derivar` | Derivar desde emergencia | EmergencyStaffController |
| POST | `/api/uti/{id}/trasladar` | Trasladar desde UTI | UtiOperativoController |
| POST | `/api/uti/{id}/alta-clinica` | Alta clínica UTI | UtiOperativoController |

---

## Consideraciones Importantes

### 1. Validación de Recursos

Antes de derivar, el sistema valida disponibilidad:

```php
private function validarRecursos(string $destino): array
{
    switch ($destino) {
        case 'cirugia':
            // Verificar quirófanos disponibles
        case 'uti':
            // Verificar camas UTI disponibles
        case 'hospitalizacion':
            // Verificar camas de hospitalización
    }
}
```

### 2. Historial de Flujo

Cada traslado se registra en `flujo_historial` (JSON):

```json
{
  "fecha": "2026-05-02 10:30:00",
  "desde": "internacion",
  "hasta": "uti",
  "usuario_id": 123,
  "notas": "Paciente requiere atención intensiva"
}
```

### 3. Cuenta Unificada

- Una sola cuenta por paciente activo
- Cargos de todas las áreas acumulados
- Seguro aplicado una sola vez
- Pago único al finalizar todas las atenciones

### 4. Estados Excluyentes

Las consultas filtran pacientes trasladados:

```php
// No mostrar pacientes trasladados en listado
Hospitalizacion::where('estado', '!=', 'trasladado')

// No mostrar pacientes trasladados en estadísticas
UtiAdmission::where('estado', '!=', 'trasladado')
```

---

*Documento actualizado con traslados entre áreas - Mayo 2026*
