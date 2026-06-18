<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Descripciones legibles de la bitácora
    |--------------------------------------------------------------------------
    |
    | Mapa de nombre de ruta => frase de acción en lenguaje natural. La vista
    | antepone el usuario, de modo que el resultado se lee:
    |
    |     "{Usuario} {frase}"  ->  "Administrador importó medicamentos al inventario"
    |
    | Es la fuente única: para que una nueva acción salga legible, basta con
    | agregar su ruta aquí. Si una ruta no está mapeada, AuditMiddleware genera
    | un texto genérico ("registró un elemento en {Módulo}") sin exponer el
    | nombre técnico de la ruta.
    |
    */
    'descriptions' => [

        // --- Administración / Almacén ---
        'admin.ajustes-pacientes.cargos.store'            => 'agregó un cargo a una cuenta',
        'admin.almacen-inventario.store'                  => 'registró un ítem de inventario',
        'admin.almacen-inventario.update'                 => 'actualizó un ítem de inventario',
        'admin.almacen-inventario.destroy'                => 'eliminó un ítem de inventario',
        'admin.almacen-medicamentos.store'                => 'registró un medicamento',
        'admin.almacen-medicamentos.update'               => 'actualizó un medicamento',
        'admin.almacen-medicamentos.destroy'              => 'eliminó un medicamento',
        'admin.almacen-medicamentos.actualizar-stock'     => 'actualizó el stock de un medicamento',
        'admin.almacen-medicamentos.ajuste-inventario.procesar' => 'realizó un ajuste de inventario',
        'admin.almacen-medicamentos.dispensar'            => 'dispensó medicamentos',
        'admin.almacen-medicamentos.importar.confirmar'   => 'importó medicamentos al inventario',
        'admin.almacen-medicamentos.lote.store'           => 'registró un lote de medicamento',
        'admin.almacen-medicamentos.registrar-paciente'   => 'registró medicamentos a un paciente',
        'admin.almacen-medicamentos.transferir.procesar'  => 'transfirió medicamentos entre almacenes',
        'admin.anulaciones.revertir'                       => 'revirtió una anulación',
        'admin.camillas.store'                             => 'registró una camilla',
        'admin.camillas.update'                            => 'actualizó una camilla',
        'admin.camillas.destroy'                           => 'eliminó una camilla',
        'admin.cargos.anular'                              => 'anuló un cargo',
        'admin.cirujanos.store'                            => 'registró un cirujano',
        'admin.cirujanos.update'                           => 'actualizó un cirujano',
        'admin.cirujanos.destroy'                          => 'eliminó un cirujano',
        'admin.cuentas.api.pago'                           => 'registró un pago de cuenta',
        'admin.doctors.store'                              => 'registró un médico',
        'admin.doctors.update'                             => 'actualizó un médico',
        'admin.doctors.destroy'                            => 'eliminó un médico',
        'admin.especialidades.store'                       => 'registró una especialidad',
        'admin.especialidades.update'                      => 'actualizó una especialidad',
        'admin.especialidades.destroy'                     => 'eliminó una especialidad',
        'admin.ingreso-precios.update'                     => 'actualizó precios de ingreso',
        'admin.neonato.cunas.store'                        => 'registró una cuna de neonatología',
        'admin.neonato.cunas.update'                       => 'actualizó una cuna de neonatología',
        'admin.neonato.cunas.destroy'                      => 'eliminó una cuna de neonatología',
        'admin.patients.update'                            => 'actualizó datos de un paciente',
        'admin.procedimientos.store'                       => 'registró un procedimiento',
        'admin.procedimientos.update'                      => 'actualizó un procedimiento',
        'admin.procedimientos.destroy'                     => 'eliminó un procedimiento',
        'admin.seguros.store'                              => 'registró un seguro',
        'admin.seguros.update'                             => 'actualizó un seguro',
        'admin.seguros.destroy'                            => 'eliminó un seguro',
        'admin.seguros.api.cambiar-estado'                 => 'cambió el estado de un seguro',

        // --- Caja ---
        'caja.contabilidad.egresos.store'                  => 'registró un egreso',
        'caja.contabilidad.egresos.destroy'                => 'eliminó un egreso',
        'caja.gestion.anular'                              => 'anuló un cobro',
        'caja.operativa.abrir'                             => 'abrió la caja',
        'caja.operativa.cerrar'                            => 'cerró la caja',
        'caja.operativa.procesar-cobro'                    => 'procesó un cobro',

        // --- Consulta externa ---
        'consulta.iniciar'                                 => 'inició una consulta',
        'consulta.completar'                               => 'completó una consulta',
        'medico.atender-paciente'                          => 'atendió a un paciente',

        // --- Farmacia ---
        'farmacia.clientes.store'                          => 'registró un cliente de farmacia',
        'farmacia.clientes.update'                         => 'actualizó un cliente de farmacia',
        'farmacia.clientes.destroy'                        => 'eliminó un cliente de farmacia',
        'farmacia.inventario.store'                        => 'registró inventario de farmacia',
        'farmacia.inventario.update'                       => 'actualizó inventario de farmacia',
        'farmacia.inventario.destroy'                      => 'eliminó inventario de farmacia',
        'farmacia.pos.procesar'                            => 'realizó una venta en farmacia',
        'farmacia.ventas.destroy'                          => 'anuló una venta de farmacia',

        // --- Quirófano ---
        'quirofano.store'                                  => 'programó una cirugía',
        'quirofano.update'                                 => 'actualizó una cirugía',
        'quirofano.cancelar'                               => 'canceló una cirugía',
        'quirofano.ejecutar'                               => 'ejecutó una cirugía',
        'quirofano.iniciar-emergencia'                     => 'inició una cirugía de emergencia',
        'quirofano.store-emergencia'                       => 'programó una cirugía de emergencia',
        'quirofano.detalles.update'                        => 'actualizó los detalles de una cirugía',
        'quirofano.equipos-medicos.agregar'                => 'agregó equipos médicos a una cirugía',
        'quirofano.medicamentos.agregar'                   => 'agregó medicamentos a una cirugía',
        'quirofano.medicamentos.store'                     => 'registró un medicamento de quirófano',
        'quirofano.medicamentos.update'                    => 'actualizó un medicamento de quirófano',
        'quirofano.medicamentos.destroy'                   => 'eliminó un medicamento de quirófano',

        // --- Recepción ---
        'reception.actualizar-hospitalizacion'             => 'actualizó una hospitalización',
        'reception.cancelar-cita'                          => 'canceló una cita',
        'reception.citas.update'                           => 'actualizó una cita',
        'reception.completar-datos-paciente.store'         => 'completó los datos de un paciente',
        'reception.confirmar-cita'                         => 'confirmó una cita',
        'reception.dar-alta'                               => 'dio de alta a un paciente',
        'reception.eliminar-cita'                          => 'eliminó una cita',
        'reception.ingreso-general.crear-especialidad'     => 'creó una especialidad',
        'reception.ingreso-general.crear-medico'           => 'creó un médico',
        'reception.ingreso-general.procesar'               => 'registró un ingreso de paciente',
        'reception.marcar-asistida'                        => 'marcó una cita como asistida',
        'reception.marcar-no-asistida'                     => 'marcó una cita como no asistida',
        'reception.nueva-cita'                             => 'creó una cita',
        'reception.procesar-pago'                          => 'procesó un pago',
        'reception.registrar-garante'                      => 'registró un garante',
        'reception.registrar-llamada'                      => 'registró una llamada',
        'reception.registrar-llegada'                      => 'registró la llegada de un paciente',
        'reception.registrar-paciente-cita'               => 'registró un paciente para una cita',
        'reception.restaurar-cita'                         => 'restauró una cita',

        // --- Seguridad ---
        'seguridad.accesos.store'                          => 'agregó una regla de acceso por IP',
        'seguridad.accesos.destroy'                        => 'eliminó una regla de acceso por IP',
        'seguridad.accesos.mode'                           => 'cambió el modo de control de acceso',
        'seguridad.backup.crear'                           => 'creó un respaldo',
        'seguridad.backup.eliminar'                        => 'eliminó un respaldo',
        'seguridad.backup.restaurar'                       => 'restauró un respaldo',
        'seguridad.backup.configuracion'                   => 'actualizó la configuración de respaldos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rutas ignoradas (POST de solo lectura)
    |--------------------------------------------------------------------------
    |
    | Endpoints que usan POST pero no mutan datos (búsquedas, filtros,
    | previsualizaciones, consultas de disponibilidad). No son acciones
    | auditables y solo generarían ruido en la bitácora.
    |
    */
    'ignore' => [
        'reception.buscar-garante-exacto',
        'quirofano.disponibilidad',
        'quirofano.medicamentos.stock',
        'farmacia.reporte.filtrar',
        'admin.almacen-medicamentos.importar.previsualizar',
    ],

];
