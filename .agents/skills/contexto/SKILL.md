---
name: contexto
description: Skill especializada en analizar completamente el contexto de un proyecto antes de implementar cambios. Prioriza comprender la arquitectura existente para realizar modificaciones mínimas, limpias y seguras.
---
# Contexto

## Descripción
Skill especializada en analizar completamente el contexto de un proyecto antes de implementar cambios.
Prioriza comprender la arquitectura existente (base de datos, modelos, controladores, vistas, rutas y flujo actual) para realizar modificaciones mínimas, limpias y seguras, evitando romper funcionalidades ya implementadas.

## Instrucciones / Reglas de la Skill

Antes de implementar cualquier funcionalidad, cambio o corrección:

### 1. ENTENDER EL CONTEXTO COMPLETO PRIMERO
- Nunca empieces programando inmediatamente.
- Primero analiza la estructura actual del proyecto.
- Revisa:
  - Base de datos
  - Migraciones
  - Modelos
  - Relaciones
  - Controladores
  - Requests/Validaciones
  - Rutas
  - Vistas
  - Componentes
  - Middleware
  - Servicios existentes
  - Helpers
  - Flujo actual de negocio

### 2. RESPETAR LA IMPLEMENTACIÓN EXISTENTE
- Nunca reemplaces código innecesariamente.
- Nunca reestructures módulos completos si no es requerido.
- No rompas compatibilidad con funcionalidades actuales.
- Mantén el estilo y patrones ya utilizados en el proyecto.
- Si existe una solución ya implementada parcialmente, reutilízala.

### 3. IMPLEMENTACIÓN MÍNIMA Y LIMPIA
- Siempre elige la solución más pequeña posible.
- Si puede resolverse con:
  - una línea → usa una línea
  - una condición → no crees una arquitectura nueva
  - reutilizar una función existente → no dupliques lógica
- Evita sobreingeniería.
- No crees clases, servicios o funciones innecesarias.

### 4. PLANIFICAR ANTES DE MODIFICAR
Antes de escribir código:
- Explica:
  - qué entendiste
  - qué partes del sistema están involucradas
  - qué impacto tendrá el cambio
  - cuál será el plan mínimo de implementación
- Luego recién implementa.

### 5. EVITAR DAÑOS COLATERALES
- Evalúa posibles efectos secundarios.
- Verifica compatibilidad con:
  - rutas existentes
  - consultas actuales
  - vistas
  - validaciones
  - relaciones de base de datos
- No elimines código existente salvo que sea estrictamente necesario.

### 6. PRIORIZAR CONSISTENCIA
- Sigue:
  - nomenclatura existente
  - convenciones del proyecto
  - estructura actual
  - estilo de código ya usado
- No mezcles patrones distintos sin necesidad.

### 7. CAMBIOS GRADUALES
- Prefiere cambios pequeños y seguros.
- Divide implementaciones grandes en pasos.
- Evita refactors masivos innecesarios.

### 8. CUANDO DETECTES PROBLEMAS
- Primero explica el problema.
- Luego muestra:
  - la causa
  - el impacto
  - la solución mínima recomendada

### 9. EN LARAVEL O FRAMEWORKS MVC
Analiza siempre primero:
- rutas
- controlador involucrado
- modelo relacionado
- migraciones
- relaciones
- vistas/formularios
- requests
- middleware
- flujo de datos completo

### 10. PRIORIDAD PRINCIPAL
La prioridad NO es escribir más código.
La prioridad es:
- entender
- preservar
- simplificar
- implementar lo mínimo necesario
- evitar romper el sistema existente.
- Siempre llama a la skill @laravel-specialist cuando trabajes en Laravel.
## rules
aplica todas las operaciones matemaicas  con bcmath php
:bcadd — Suma dos números de precisión arbitrária
bcceil — Redondea al alza un número de precisión arbitraria
bccomp — Comparar dos números de gran tamaño
bcdiv — Divide dos números de precisión arbitraria
bcdivmod — Devuelve el cociente y el resto de un número de precisión arbitraria
bcfloor — Redondea hacia abajo un número de precisión arbitraria
bcmod — Devuelve el resto de una división entre números de gran tamaño
bcmul — Multiplica dos números de precisión arbitraria
bcpow — Elevar un número de precisión arbitraria a otro
bcpowmod — Eleva un número de precisión arbitraria a otro, reducido por un módulo especificado
bcround — Redondea un número de precisión arbitraria
bcscale — Define o recupera la precisión por defecto para todas las funciones bc math
bcsqrt — Obtiene la raiz cuadrada de un número de precisión arbitraria
bcsub — Resta un número de precisión arbitraria de otro