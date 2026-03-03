# API del Módulo de Farmacia

## Endpoints Disponibles

### Farmacia
- `GET /farmacia/api/farmacias` - Listar todas las farmacias
- `POST /farmacia/api/farmacias` - Crear nueva farmacia
- `GET /farmacia/api/farmacias/{id}` - Obtener farmacia específica
- `PUT /farmacia/api/farmacias/{id}` - Actualizar farmacia
- `DELETE /farmacia/api/farmacias/{id}` - Eliminar farmacia

### Medicamentos
- `GET /farmacia/api/medicamentos` - Listar todos los medicamentos
- `POST /farmacia/api/medicamentos` - Crear nuevo medicamento
- `GET /farmacia/api/medicamentos/{codigo}` - Obtener medicamento específico
- `PUT /farmacia/api/medicamentos/{codigo}` - Actualizar medicamento
- `DELETE /farmacia/api/medicamentos/{codigo}` - Eliminar medicamento

### Detalle Medicamentos
- `GET /farmacia/api/detalle-medicamentos` - Listar todos los detalles de medicamentos
- `POST /farmacia/api/detalle-medicamentos` - Crear nuevo detalle de medicamento
- `GET /farmacia/api/detalle-medicamentos/{id_farmacia}/{codigo_medicamentos}` - Obtener detalle específico
- `PUT /farmacia/api/detalle-medicamentos/{id_farmacia}/{codigo_medicamentos}` - Actualizar detalle
- `DELETE /farmacia/api/detalle-medicamentos/{id_farmacia}/{codigo_medicamentos}` - Eliminar detalle

### Insumos
- `GET /farmacia/api/insumos` - Listar todos los insumos
- `POST /farmacia/api/insumos` - Crear nuevo insumo
- `GET /farmacia/api/insumos/{codigo}` - Obtener insumo específico
- `PUT /farmacia/api/insumos/{codigo}` - Actualizar insumo
- `DELETE /farmacia/api/insumos/{codigo}` - Eliminar insumo

### Detalle Insumos
- `GET /farmacia/api/detalle-insumos` - Listar todos los detalles de insumos
- `POST /farmacia/api/detalle-insumos` - Crear nuevo detalle de insumo
- `GET /farmacia/api/detalle-insumos/{id_farmacia}/{codigo_insumos}` - Obtener detalle específico
- `PUT /farmacia/api/detalle-insumos/{id_farmacia}/{codigo_insumos}` - Actualizar detalle
- `DELETE /farmacia/api/detalle-insumos/{id_farmacia}/{codigo_insumos}` - Eliminar detalle

### Detalle Receta
- `GET /farmacia/api/detalle-receta` - Listar todos los detalles de recetas
- `POST /farmacia/api/detalle-receta` - Crear nuevo detalle de receta
- `GET /farmacia/api/detalle-receta/{id_farmacia}/{codigo_medicamentos}` - Obtener detalle específico
- `PUT /farmacia/api/detalle-receta/{id_farmacia}/{codigo_medicamentos}` - Actualizar detalle
- `DELETE /farmacia/api/detalle-receta/{id_farmacia}/{codigo_medicamentos}` - Eliminar detalle

### Inventario
- `GET /farmacia/api/inventario-data` - Listar todo el inventario
- `POST /farmacia/api/inventario-data` - Crear nuevo registro de inventario
- `GET /farmacia/api/inventario-data/{id}/{id_farmacia}` - Obtener registro específico
- `PUT /farmacia/api/inventario-data/{id}/{id_farmacia}` - Actualizar registro
- `DELETE /farmacia/api/inventario-data/{id}/{id_farmacia}` - Eliminar registro

### Caja Farmacia
- `GET /farmacia/api/caja-farmacia` - Listar todas las cajas de farmacia
- `POST /farmacia/api/caja-farmacia` - Crear nueva caja de farmacia
- `GET /farmacia/api/caja-farmacia/{codigo}` - Obtener caja específica
- `PUT /farmacia/api/caja-farmacia/{codigo}` - Actualizar caja
- `DELETE /farmacia/api/caja-farmacia/{codigo}` - Eliminar caja

## Ejemplos de Uso

### Crear una nueva farmacia
```json
POST /farmacia/api/farmacias
{
    "ID": "F3",
    "DETALLE": "Farmacia Sur"
}
```

### Crear un nuevo medicamento
```json
POST /farmacia/api/medicamentos
{
    "CODIGO": "M3",
    "DESCRIPCION": "Aspirina",
    "PRECIO": 12.50
}
```

### Crear un detalle de medicamento
```json
POST /farmacia/api/detalle-medicamentos
{
    "ID_FARMACIA": "F1",
    "CODIGO_MEDICAMENTOS": "M1",
    "LABORATORIO": "Bayer",
    "FECHA_VENCIMIENTO": "2025-12-31",
    "TIPO": "Analgésico",
    "REQUERIMIENTO": "Receta médica"
}
```

## Notas Importantes

- Todos los endpoints requieren autenticación y rol de administrador
- Las respuestas incluyen las relaciones cargadas cuando se especifica `with()`
- Los campos numéricos como PRECIO y SUBTOTAL se devuelven como tipo float
- Las fechas siguen el formato YYYY-MM-DD
- Los IDs compuestos en las tablas de detalle se manejan con múltiples parámetros en la URL
