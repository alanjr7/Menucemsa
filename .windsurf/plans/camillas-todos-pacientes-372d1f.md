# Plan: Mostrar todos los pacientes en Camillas de Emergencia

Modificar `/emergency-staff/camillas` para mostrar todos los pacientes existentes con el mismo formato de `/patients`, agregando el botón "Registrar uso de camilla".

## Cambios necesarios

### 1. Controlador: `CamillaUsoController::index()`
- Eliminar filtro `whereHas('emergencias', ...)`
- Obtener todos los pacientes con paginación como en `PatientsController`
- Mantener la carga de camillas activas

### 2. Vista: `emergency-staff/camillas/index.blade.php`
- Reestructurar tabla con columnas: Código, Nombre Completo, Carnet, Seguro, Ingreso, Acciones
- Agregar botón "Registrar uso de camilla" (naranja) en columna Acciones
- Mantener el modal de registro de camilla (funcionalidad existente)
- Preservar la lógica Alpine.js para el modal

### 3. Consideraciones
- El botón "Registrar uso de camilla" solo aparece si hay camillas disponibles
- Se mantiene la funcionalidad del modal con el formulario de camilla
- Se preserva la paginación

## Archivos a modificar
- `app/Http/Controllers/EmergencyStaff/CamillaUsoController.php`
- `resources/views/emergency-staff/camillas/index.blade.php`

## Impacto
- Bajo - Solo cambio en presentación de datos, sin afectar BD ni otras funcionalidades
