---
description: Workflow para investigar profundamente el impacto de cambios en Laravel
---

# Workflow: Investigar Impacto de Cambios

Guía paso a paso para analizar el impacto de cambios propuestos antes de implementarlos.

## Paso 1: Definir el Cambio

Antes de investigar, clarificar:
- ¿Qué se quiere cambiar/agregar/eliminar?
- ¿Por qué se necesita este cambio?
- ¿Hay errores actuales relacionados?

## Paso 2: Activar la Skill

Usar frases de activación:
- "Analiza el impacto de..."
- "Investiga cómo afectará..."
- "Evalúa el riesgo de implementar..."

## Paso 3: Revisar el Reporte Generado

La skill proporcionará:
1. **Resumen ejecutivo** con nivel de riesgo
2. **Lista de archivos afectados** organizados por tipo
3. **Riesgos identificados** con severidad
4. **Recomendaciones** de implementación
5. **Checklist** pre-implementación

## Paso 4: Tomar Decisión Informada

Basado en el análisis:
- **Riesgo Alto**: Replanificar, buscar alternativa, crear migración segura
- **Riesgo Medio**: Proceder con precaución, seguir recomendaciones
- **Riesgo Bajo**: Implementar directamente

## Paso 5: Implementación Limpia

Si se procede, seguir:

### Para cambios en Base de Datos
// turbo
1. Crear migración con valores por defecto si es necesario
2. Verificar índices en campos nuevos de búsqueda
3. Probar rollback de migración
4. Actualizar seeders si aplica

### Para cambios en Modelos
// turbo
1. Actualizar `$fillable` y `$casts`
2. Revisar relaciones afectadas
3. Agregar scopes si es necesario
4. Verificar observers o events

### Para cambios en Vistas
// turbo
1. Validar con datos reales
2. Revisar responsividad si aplica
3. Verificar permisos de visualización
4. Testear edge cases

### Para cambios en Controladores
// turbo
1. Validar autorizaciones
2. Revisar mensajes de error
3. Verificar logging de actividades
4. Testear flujo completo

## Ejemplo Completo

**Cambio solicitado**: Agregar campo "teléfono de emergencia" a pacientes

**Investigación**:
```
Usuario: "Analiza el impacto de agregar teléfono de emergencia a pacientes"
IA: Usa skill laravel-impact-analyzer
```

**Reporte resultante**:
- Riesgo: Bajo
- Archivos: 5 (Modelo, 2 vistas, migración, request)
- Recomendación: Campo nullable, agregar validación

**Implementación**:
// turbo
1. Crear migración con campo nullable
2. Actualizar modelo Paciente (fillable)
3. Modificar formulario de registro
4. Agregar validación en Request
5. Mostrar en vista de detalle

## Buenas Prácticas

- Siempre investigar antes de cambiar tablas con datos
- Documentar breaking changes identificados
- Crear migraciones reversibles
- Validar con datos de producción (anónimos)
- Seguir principios SOLID y Clean Code
