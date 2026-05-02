---
description: Skill para investigación profunda y análisis de impacto de cambios en sistemas Laravel/PHP
triggers:
  - analiza impacto
  - investiga cambio
  - evaluar riesgo
  - análisis profundo
  - investiga error
---

# Laravel Impact Analyzer

Skill para investigación profunda y análisis de impacto de cambios en sistemas Laravel/PHP, generando reportes detallados antes de la implementación.

## Cuándo Usar Esta Skill

- Antes de implementar nuevas features
- Al refactorizar código existente
- Al modificar modelos, migraciones o relaciones
- Al agregar/eliminar campos de base de datos
- Al cambiar lógica de negocio crítica
- Al diagnosticar errores complejos
- Cuando el usuario solicita "investigar" o "analizar impacto"

## Flujo de Trabajo

### Paso 1: Recopilar Contexto

1. **Identificar el punto de entrada**
   - Preguntar al usuario qué cambio/error/feature quiere analizar
   - Solicitar archivos específicos si los mencionó
   - Obtener stack trace si es un error

2. **Investigar el código base**
   - Usar `code_search` para encontrar referencias al componente
   - Leer archivos clave (models, controllers, views)
   - Mapear dependencias y relaciones

### Paso 2: Análisis Exhaustivo

Para cada cambio propuesto, analizar:

#### Áreas de Impacto
- **Models**: Relaciones, fillable, casts, scopes, accessors
- **Migrations**: Campos, índices, foreign keys, cambios destructivos
- **Controllers**: Métodos afectados, validaciones, autorizaciones
- **Views**: Blade templates que usan los datos
- **Routes**: Rutas relacionadas
- **Services**: Clases de servicio afectadas
- **Jobs/Events/Listeners**: Procesos asíncronos
- **Tests**: Tests que pueden fallar

#### Criterios de Evaluación
| Aspecto | Qué verificar |
|---------|---------------|
| Breaking Changes | Cambios que rompen API o contratos |
| Data Integrity | Impacto en datos existentes |
| Performance | N+1 queries, falta de índices |
| Security | Validaciones, autorizaciones |
| Clean Code | SRP, nombres, complejidad, DRY |

### Paso 3: Generar Reporte

Estructura obligatoria del reporte:

```markdown
## Análisis de Impacto: [Nombre del Cambio]

### Resumen Ejecutivo
- **Nivel de Riesgo**: Alto / Medio / Bajo
- **Archivos Afectados**: N archivos
- **Breaking Changes**: Sí / No

### Archivos Identificados

#### Models
- `app/Models/X.php` - [Razón del impacto]

#### Controllers  
- `app/Http/Controllers/XController.php` - [Métodos afectados]

#### Vistas
- `resources/views/x.blade.php` - [Secciones afectadas]

#### Migraciones
- `database/migrations/xxxx_xx_xx_xxxxxx_create_x_table.php`

### Riesgos Identificados
1. **[Alto]** Descripción del riesgo y mitigación
2. **[Medio]** Descripción del riesgo y mitigación

### Recomendaciones de Implementación
1. Orden de migraciones (si aplica)
2. Estrategia de rollback
3. Tests necesarios
4. Pasos específicos para Laravel

### Checklist Pre-implementación
- [ ] Verificar índices de base de datos
- [ ] Validar permisos de usuarios
- [ ] Confirmar compatibilidad con datos existentes
- [ ] Revisar N+1 queries en relaciones
- [ ] Validar según reglas de Clean Code
```

## Evaluación de Riesgos

### Riesgo Alto 🔴
- Cambios en tablas con datos en producción sin migración segura
- Eliminación de campos usados en múltiples lugares
- Modificación de claves foráneas existentes
- Cambios en lógica de autenticación/autorización
- Breaking changes en API públicas
- Cambios en campos únicos con datos duplicados existentes

### Riesgo Medio 🟡
- Nuevos campos obligatorios sin valores por defecto
- Modificación de scopes o accessors en modelos populares
- Cambios en validaciones de formularios existentes
- Refactoring de métodos compartidos entre controladores
- Modificación de eventos/listeners

### Riesgo Bajo 🟢
- Nuevos campos opcionales (nullable)
- Nuevos métodos que no afectan existentes
- Cambios en vistas sin modificar estructura de datos
- Agregar logs, comentarios o documentación
- Nuevas rutas independientes

## Principios de Clean Code (Verificar)

Al analizar el impacto, asegurar que la implementación propuesta cumpla:

1. **Nombres descriptivos**
   - Variables, métodos y clases expresan intención
   - Sin abreviaciones confusas

2. **Funciones pequeñas**
   - Hacen una sola cosa (SRP)
   - Máximo 3 parámetros recomendado
   - Evitar lógica anidada compleja

3. **Eliminar duplicación (DRY)**
   - Detectar código similar en archivos afectados
   - Sugerir extracción a traits o services

4. **Manejo de errores explícito**
   - No ocultar errores
   - Validaciones claras

5. **Clases pequeñas**
   - Una sola responsabilidad
   - Alta cohesión, bajo acoplamiento

## Comandos de Investigación

### Búsqueda de referencias
```
code_search: {"search_term": "NombreModelo", "search_folder_absolute_uri": "c:/proyecto/app"}
grep_search: {"Query": "nombre_campo", "SearchPath": "c:/proyecto", "Includes": ["*.php", "*.blade.php"]}
list_dir: {"DirectoryPath": "c:/proyecto/app/Models"}
```

### Análisis de rutas
```
grep_search: {"Query": "NombreController", "SearchPath": "c:/proyecto/routes", "Includes": ["*.php"]}
```

### Migraciones relacionadas
```
grep_search: {"Query": "tabla_afectada", "SearchPath": "c:/proyecto/database/migrations", "Includes": ["*.php"]}
```

## Ejemplo de Uso

**Usuario**: "Necesito agregar un campo 'estado_civil' a la tabla de pacientes"

**Acciones de la skill**:
1. Buscar modelo `Paciente` y sus relaciones
2. Encontrar migraciones de pacientes
3. Identificar controladores que crean/actualizan pacientes
4. Localizar vistas con formularios de pacientes
5. Buscar referencias al campo en toda la aplicación
6. Verificar si hay seeders o factories afectados

**Reporte generado**:
- 8 archivos identificados
- 2 controladores a modificar
- 3 vistas que necesitan actualización
- Riesgo: Bajo (campo opcional)
- Recomendación: Agregar como nullable inicialmente

## Output Esperado

Siempre generar:
1. Lista completa de archivos afectados
2. Nivel de riesgo justificado
3. Recomendaciones específicas para Laravel
4. Checklist antes de implementar
5. Sugerencias de mejora según Clean Code
