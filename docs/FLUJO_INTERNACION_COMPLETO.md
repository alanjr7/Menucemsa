# Flujo Completo de Internación - Sistema Hospitalario

## Resumen Ejecutivo

Este documento describe el flujo completo de internación de un paciente desde su ingreso a la clínica hasta su alta médica y cobro final. El sistema implementa un modelo de **Cuenta Maestra** que unifica todos los servicios bajo una única cuenta por paciente.

---

## Diagramas

- **Diagrama de Actividades:** `docs/diagramas/flujo-internacion-plantuml.puml`
- **Diagrama de Secuencia:** `docs/diagramas/flujo-internacion-secuencia.puml`

---

## Fases del Flujo

### FASE 1: Ingreso y Registro (Recepción)

**Archivos principales:**
- `app/Http/Controllers/Reception/HospitalizacionController.php`
- `app/Services/CuentaCobroService.php`

**Flujo:**
1. Paciente llega a recepción
2. Recepcionista busca paciente por CI (mínimo 3 dígitos)
3. Si no existe → Se registra con datos básicos (nombres, sexo, seguro default="particular")
4. Se verifica que el paciente no tenga una hospitalización activa
5. Se crea triage amarillo (urgencia media)
6. Se genera ID de hospitalización: `HOSP-{YmdHis}-{random(100,999)}`

**Cuenta Maestra:**
```
Regla fundamental: UN paciente = UNA cuenta activa a la vez

- Si existe cuenta pendiente/parcial → Se reutiliza
- Si no existe → Se crea nueva con episodio_numero incrementado
- ID: CC-{YmdHis}-{random(0,99999)}
- Estado inicial: pendiente
- Tipo: es_post_pago = true (pago después de la atención)
```

**Cargo de admisión:**
- Tipo: servicio
- Descripción: "Admisión de Internación"
- Precio: Desde `IngresoPrecio::getPrecio('internacion')` o tarifa HOSP-ADM
- Default: Bs. 150.00

---

### FASE 2: Asignación de Habitación

**Archivos principales:**
- `app/Services/Habitacion/AsignacionCamaService.php`

**Flujo:**
1. Verificar disponibilidad de cama (`disponibilidad = 'disponible'`)
2. Crear detalle de estadía en cuenta de cobro:
   - Tipo: `estadia`
   - Descripción: "Estancia Internación - Hab. {id}, Cama {nro}"
   - Cantidad: 1 (inicial)
   - Precio unitario: según cama (`precio_por_dia`)
3. Actualizar hospitalización:
   - `habitacion_id`
   - `cama_id`
   - `precio_cama_dia`
   - `cuenta_cobro_detalle_id`
4. Marcar cama como `ocupada`
5. Actualizar estado de habitación si es necesario

---

### FASE 3: Atención Médica (Evoluciones)

**Archivos principales:**
- `app/Http/Controllers/Medical/HospitalizacionController.php`

**Flujo diario:**
1. Médico registra evolución del paciente
2. Puede agregar medicamentos:
   - Selecciona del almacén
   - Se descuenta del inventario automáticamente
   - Se agrega cargo a cuenta maestra (tipo: medicamento)
3. Puede agregar equipos médicos:
   - Nombre libre (ej: "Oxígeno", "Suero", etc.)
   - Precio definido por el médico
   - Cantidad configurable
   - Se agrega cargo a cuenta maestra (tipo: equipo_medico)
4. Se guarda historial en campo JSON `equipos_medicos`:
   ```json
   {
     "tipo": "evolucion",
     "fecha": "2026-05-02 10:30:00",
     "diagnostico": "...",
     "tratamiento": "...",
     "medicamentos": [...],
     "equipos_medicos": [...],
     "usuario_id": 123
   }
   ```

---

### FASE 4: Alta Médica y Liberación

**Archivos principales:**
- `app/Http/Controllers/Reception/HospitalizacionController.php:319-349`
- `app/Services/Habitacion/LiberacionCamaService.php`

**Flujo:**
1. Médico solicita alta médica
2. Recepción confirma motivo de alta (`motivo_alta`)
3. Sistema calcula estadía:
   ```
   días = max(1, diffInDays(fecha_ingreso, fecha_alta) + 1)
   // Mínimo 1 día aunque el alta sea el mismo día
   ```
4. Calcula costo total: `días × precio_cama_dia`
5. Actualiza detalle de estadía:
   - Cantidad: días reales
   - Subtotal: costo total
6. Libera cama:
   - `disponibilidad = 'disponible'`
   - Actualiza estado de habitación
7. Marca hospitalización con `fecha_alta`

---

### FASE 5: Cobro en Caja

**Archivos principales:**
- `app/Http/Controllers/Caja/CajaOperativaController.php:426-600`
- `app/Services/AplicarSeguroService.php`

**Flujo:**
1. Cajero abre caja (CajaSession)
2. Busca cuenta del paciente
3. Sistema aplica seguro automáticamente si corresponde:

**Tipos de cobertura de seguro:**

| Tipo | Descripción |
|------|-------------|
| `porcentaje` | Cubre X%, paciente paga el resto |
| `solo_consulta` | Solo cubre consulta externa |
| `tope_monto` | Cubre hasta un monto máximo |

**Estados de seguro en cuenta:**
- `null` = Sin seguro
- `pendiente_autorizacion` = Esperando aprobación
- `autorizado` = Aprobado y aplicado
- `rechazado` = Rechazado

**Cálculo de montos:**
```
saldo_pendiente = total_calculado - seguro_cobertura - total_pagado
```

**Métodos de pago:**
- Efectivo
- Transferencia
- Tarjeta
- QR

**Estados de cuenta:**
- `pendiente`: Sin pagos
- `parcial`: Pagos parciales o seguro con copago pendiente
- `pagado`: Total cubierto

---

## Modelos de Datos Clave

### Hospitalización

| Campo | Descripción |
|-------|-------------|
| `id` | HOSP-{YmdHis}-{random} |
| `ci_paciente` | CI del paciente |
| `ci_medico` | CI del médico tratante |
| `habitacion_id` | Referencia a habitación |
| `cama_id` | Referencia a cama |
| `precio_cama_dia` | Precio por día de la cama |
| `total_estancia` | Costo total de estadía |
| `fecha_ingreso` | Fecha de ingreso |
| `fecha_alta` | Fecha de alta (null si está activa) |
| `estado` | activo/inactivo |
| `motivo` | Motivo de internación |
| `diagnostico` | Diagnóstico médico |
| `equipos_medicos` | JSON con historial de evoluciones |

### CuentaCobro (Cuenta Maestra)

| Campo | Descripción |
|-------|-------------|
| `id` | CC-{YmdHis}-{random} |
| `paciente_ci` | CI del paciente |
| `tipo_atencion` | internacion/emergencia/consulta_externa/etc |
| `estado` | pendiente/parcial/pagado |
| `total_calculado` | Suma de todos los detalles |
| `total_pagado` | Suma de pagos registrados |
| `es_post_pago` | true para internación/emergencia |
| `episodio_numero` | Número de ingreso del paciente |
| `seguro_id` | ID del seguro (si aplica) |
| `seguro_estado` | null/pendiente_autorizacion/autorizado/rechazado |
| `seguro_monto_cobertura` | Monto cubierto por seguro |
| `seguro_monto_paciente` | Monto a pagar por paciente |

### CuentaCobroDetalle (Items de la Cuenta)

| Campo | Descripción |
|-------|-------------|
| `cuenta_cobro_id` | Referencia a cuenta maestra |
| `tipo_item` | servicio/medicamento/estadia/equipo_medico/etc |
| `descripcion` | Descripción del item |
| `cantidad` | Cantidad |
| `precio_unitario` | Precio por unidad |
| `subtotal` | cantidad × precio_unitario |
| `origen_type` | Clase del modelo origen |
| `origen_id` | ID del registro origen |
| `area_origen` | internacion/emergencia/quirofano/etc |

---

## Servicios Clave

### CuentaCobroService

**Métodos principales:**

| Método | Descripción |
|--------|-------------|
| `obtenerOCrearCuentaMaestra()` | Obtiene o crea la cuenta activa del paciente |
| `agregarCargoConDeduplicacion()` | Agrega cargo evitando duplicados |
| `crearCuentaInternacion()` | Crea cuenta específica para internación |
| `agregarCargoEstadia()` | Agrega cargo por estadía |
| `obtenerCuentaPostPagoActiva()` | Busca cuenta post-pago pendiente |

### AsignacionCamaService

| Método | Descripción |
|--------|-------------|
| `asignar()` | Asigna cama y crea detalle de estadía |
| `obtenerOCrearCuenta()` | Obtiene o crea cuenta para la hospitalización |

### LiberacionCamaService

| Método | Descripción |
|--------|-------------|
| `liberar()` | Libera cama y calcula estadía final |
| `finalizarEstadia()` | Actualiza detalle con días reales |

### AplicarSeguroService

| Método | Descripción |
|--------|-------------|
| `aplicarSiCorresponde()` | Aplica seguro automáticamente |
| `calcularProyeccion()` | Calcula proyección sin persistir |

---

## Estados y Transiciones

### Estados de Hospitalización

```
[Ingreso] → [Activa] → [Alta Médica]
              ↓
         [Estadía en curso]
```

### Estados de CuentaCobro

```
[Creación] → [Pendiente] → [Parcial] → [Pagado]
                 ↓             ↓
            [Con seguro]  [Con copago]
                 ↓
            [Rechazado] → [Pago total paciente]
```

### Estados de Seguro en Cuenta

```
[null] → [pendiente_autorizacion] → [autorizado] → [aplicado en cuenta]
              ↓
         [rechazado]
```

---

## Consideraciones Importantes

### Cobro de Estadía
- Se cobra el día de ingreso + días completos adicionales
- Mínimo 1 día aunque el alta sea el mismo día (regla de negocio)
- Fórmula: `max(1, diffInDays(fecha_ingreso, fecha_alta) + 1)`

### Unificación de Cuentas
- Si un paciente tiene emergencia e internación, ambas van a la misma cuenta maestra
- Se evitan duplicados mediante `agregarCargoConDeduplicacion()`
- Se verifica por `origen_type + origen_id + tipo_item`

### Seguros
- Solo aplican seguros con `tipo_cobertura = porcentaje|tope_monto`
- El seguro "particular" no se considera seguro aplicable
- La aplicación es automática en caja si el seguro está activo

### Permisos por Rol
- **Recepción:** Ingreso, alta, asignación de habitaciones
- **Médico:** Evoluciones, medicamentos, equipos médicos
- **Caja:** Cobros, aplicación de seguros, cierre de cuentas

---

## Archivos Relacionados

| Categoría | Archivos |
|-----------|----------|
| **Controladores** | `Reception/HospitalizacionController.php`<br>`Medical/HospitalizacionController.php`<br>`Caja/CajaOperativaController.php` |
| **Servicios** | `CuentaCobroService.php`<br>`AsignacionCamaService.php`<br>`LiberacionCamaService.php`<br>`AplicarSeguroService.php` |
| **Modelos** | `Hospitalizacion.php`<br>`CuentaCobro.php`<br>`CuentaCobroDetalle.php`<br>`Seguro.php`<br>`Cama.php`<br>`Habitacion.php` |
| **Vistas** | `reception/hospitalizacion.blade.php`<br>`medical/internacion-detalle.blade.php`<br>`caja/operativa.blade.php` |

---

## Notas de Implementación

1. **Transacciones:** Todas las operaciones críticas usan `DB::transaction()` para garantizar integridad
2. **Logs:** El sistema registra logs en operaciones importantes (creación de cuentas, cargos duplicados, etc.)
3. **Validaciones:** Se valida stock de medicamentos antes de descontar
4. **Cálculos:** Los subtotales se calculan automáticamente mediante observers del modelo
5. **JSON:** El historial médico se almacena en formato JSON para flexibilidad

---

*Documento generado tras investigación profunda del código - Mayo 2026*
