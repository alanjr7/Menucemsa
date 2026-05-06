# Plan: Actualizar Impresión de Confirmación de Registro

## Resumen
Modificar la sección de impresión de la vista `confirmacion-registro.blade.php` para mostrar todos los datos del paciente, tipo de ingreso, doctor, especialidad, fecha y hora, manteniendo el estilo de impresión actual.

## Análisis Actual

### Flujo de Datos
- El `IngresoGeneralController` crea consultas externas que redirigen a `confirmacion-registro/{caja_id}`
- El `ReceptionController::confirmacionRegistro()` carga la relación completa: `consulta.paciente.seguro`, `consulta.medico.user`, `consulta.especialidad`
- La impresión actual muestra solo datos básicos del paciente y campos vacíos de internación (inapropiados)

### Datos Disponibles
```
$caja->tipo → 'CONSULTA_EXTERNA'
$caja->fecha → Fecha de registro
$caja->consulta->hora → Hora de la consulta
$caja->consulta->medico->user->name → Nombre del doctor
$caja->consulta->especialidad->nombre → Especialidad
n$caja->consulta->paciente → Todos los datos del paciente
```

## Cambios Requeridos

### 1. Sección de Impresión (líneas 276-581)

#### Datos del Paciente - Completar campos faltantes:
- Lugar de expedición (lugar_expedicion)
- Sexo completo (Masculino/Femenino en lugar de M/F)
- Correo electrónico
- Profesión
- Empresa de trabajo
- Seguro (nombre del seguro)

#### Sección de Ingreso - Nueva sección a agregar:
- **Tipo de Ingreso**: "CONSULTA EXTERNA" (formateado desde $caja->tipo)
- **Médico**: Dr. [nombre del médico]
- **Especialidad**: [nombre de especialidad]
- **Fecha**: [formato d/m/Y desde $caja->consulta->fecha]
- **Hora**: [hora desde $caja->consulta->hora]
- **Motivo**: [motivo de consulta]
- **Código de Consulta**: [código único]

#### Sección "Causa o Motivo de Internación" (líneas 453-489):
- **ELIMINAR** - No aplica para consulta externa, genera confusión

#### Sección "Garante" (líneas 386-451):
- **MANTENER** solo si el paciente tiene garante asociado
- Mostrar condicionalmente

#### Firmas y Actas (líneas 492-578):
- **MANTENER** - Estructura legal de aceptación

## Implementación

### Fase 1: Reestructurar Datos del Paciente
```blade
<!-- Datos completos del paciente -->
- Nombre completo
- CI con lugar de expedición
- Fecha de nacimiento (edad calculada)
- Sexo (Masculino/Femenino)
- Estado civil
- Nacionalidad
- Teléfono
- Correo
- Profesión
- Empresa
- Dirección
- Seguro
```

### Fase 2: Nueva Sección "Datos del Ingreso"
```blade
<!-- Nueva sección entre paciente y garante -->
<h4 class="section-title">DATOS DEL INGRESO</h4>
<div class="form-section">
    - Tipo: CONSULTA EXTERNA
    - Código: {{ $caja->consulta->codigo }}
    - Médico: Dr. {{ $caja->consulta->medico->user->name }}
    - Especialidad: {{ $caja->consulta->especialidad->nombre }}
    - Fecha: {{ fecha formateada }}
    - Hora: {{ hora }}
    - Motivo: {{ motivo consulta }}
</div>
```

### Fase 3: Eliminar Sección de Internación
- Remover checkbox de Cirugía/Tratamiento
- Remover campos de médico que dispone internación
- Remover diagnóstico preliminar
- Remover habitación asignada

## Archivos a Modificar
1. `resources/views/reception/confirmacion-registro.blade.php` - Sección `.print-only` (líneas ~276-581)

## Riesgos y Mitigación

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Datos nulos causen errores | Media | Medio | Usar operador null safe `?->` y valores por defecto |
| Formato de impresión roto | Baja | Medio | Mantener clases CSS existentes |
| Campos duplicados | Baja | Bajo | Revisar layout final antes de entregar |

## Checklist QA
- [ ] Todos los campos del paciente se muestran correctamente
- [ ] Tipo de ingreso muestra "CONSULTA EXTERNA" formateado
- [ ] Nombre del médico aparece con prefijo "Dr."
- [ ] Especialidad se muestra correctamente
- [ ] Fecha y hora usan formato legible (d/m/Y H:i)
- [ ] Sección de internación eliminada
- [ ] Garante aparece solo cuando existe
- [ ] Impresión mantiene estilo visual actual
- [ ] Sin errores de null pointer

## Notas
- El estilo visual actual (líneas punteadas, tipografía, márgenes) se conserva
- Se mantiene compatibilidad con el sistema de impresión existente (función `imprimir()`)
