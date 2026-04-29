<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\CuentaCobro;
use App\Models\Caja;
use App\Models\Seguro;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ConsultaExternaController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::query()
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $seguros = Seguro::where('estado', 'activo')
            ->orderBy('nombre_empresa')
            ->get();

        return view('reception.consulta-externa', compact('especialidades', 'seguros'));
    }

    public function buscarPaciente(Request $request)
    {
        $ci = $request->get('ci');

        if (!is_numeric($ci) || strlen($ci) < 7 || strlen($ci) > 10) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe ser un número de 7 a 10 dígitos'
            ]);
        }

        $paciente = Paciente::with(['seguro', 'triage', 'registro'])
                            ->where('ci', $ci)
                            ->first();

        if ($paciente) {
            return response()->json([
                'success' => true,
                'paciente' => $paciente,
                'message' => 'Paciente encontrado'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado. Por favor registre sus datos.',
                'show_form' => true
            ]);
        }
    }

    public function registrarConsultaExterna(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Crear o encontrar paciente
            $paciente = $this->crearOActualizarPaciente($request);

            // 2. Verificar si ya existe un registro pendiente para este paciente hoy
            $registroExistente = Caja::where('tipo', 'CONSULTA_EXTERNA')
                ->whereDate('fecha', today())
                ->whereNull('nro_factura') // Pendiente de pago
                ->whereHas('consulta', function($query) use ($paciente) {
                    $query->where('ci_paciente', $paciente->ci);
                })
                ->first();

            if ($registroExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene una consulta registrada y pendiente de pago para hoy. Por favor, diríjase a caja para completar el pago.',
                    'redirect_url' => route('reception.confirmacion-registro', ['id' => $registroExistente->id])
                ], 422);
            }

            // 3. Crear registro de consulta en caja (estado pendiente)
            $caja = $this->crearRegistroCajaPendiente($request, $paciente);

            // 4. Crear consulta
            $consulta = $this->crearConsulta($request, $paciente, $caja);

            // 5. Crear cuenta por cobrar para caja
            $this->crearCuentaCobro($request, $paciente, $consulta, $caja);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consulta externa registrada exitosamente. El paciente debe pasar a caja para realizar el pago.',
                'consulta' => $consulta->load(['paciente', 'medico.user', 'especialidad']),
                'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearOActualizarPaciente($request)
    {
        $ci = $request->ci;
        
        // Buscar paciente existente
        $paciente = Paciente::find($ci);
        
        if (!$paciente) {
            // Validar que todos los campos requeridos estén presentes
            $request->validate([
                'nombres' => 'required|string|max:80',
                'apellidos' => 'required|string|max:80',
                'sexo' => 'required|string|in:Masculino,Femenino',
            ]);

            $seguroId = $request->seguro_id ?: $this->obtenerOCrearSeguro('Particular');

            // Crear nuevo paciente con todos los campos requeridos
            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => trim($request->nombres . ' ' . $request->apellidos),
                'sexo' => $request->sexo === 'Femenino' ? 'F' : 'M',
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ?? 0,
                'correo' => $request->correo ?? 'sin@email.com',
                'seguro_id' => $seguroId,
                'triage_id' => $this->obenerOCrearTriage(),
                'registro_codigo' => $this->obtenerOCrearRegistro(),
            ]);
        } else {
            // Actualizar datos si es necesario
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
            ]);
        }
        
        return $paciente;
    }

    private function crearRegistroCajaPendiente($request, $paciente)
    {
        $costoConsulta = $this->obtenerPrecioConsultaExterna();

        return Caja::create([
            'fecha' => now(),
            'total_dia' => $costoConsulta,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => null,
            'farmacia_id' => null,
            'monto_pagado' => $costoConsulta,
        ]);
    }

    private function crearConsulta($request, $paciente, $caja)
    {
        // Obtener médico basado en la selección
        $medicoId = $this->obtenerMedicoId($request->medico);
        
        return Consulta::create([
            'codigo' => 'CONS-' . date('Y') . '-' . str_pad(Consulta::count() + 1, 6, '0', STR_PAD_LEFT),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones ?? '',
            'codigo_especialidad' => $this->obtenerEspecialidadCodigo($request->especialidad),
            'ci_paciente' => $paciente->ci,
            'ci_medico' => $medicoId,
            'estado_pago' => false,
            'caja_id' => $caja->id,
            'estado' => 'pendiente',
        ]);
    }

    private function crearCuentaCobro($request, $paciente, $consulta, $caja)
    {
        $costoConsulta = $this->obtenerPrecioConsultaExterna();

        $tieneSeguro = !empty($request->seguro_id);
        
        $cuenta = \App\Models\CuentaCobro::create([
            'paciente_ci' => $paciente->ci,
            'tipo_atencion' => 'consulta',
            'referencia_id' => $consulta->codigo,
            'referencia_type' => Consulta::class,
            'total_calculado' => $costoConsulta,
            'total_pagado' => 0,
            'estado' => $tieneSeguro ? 'pendiente' : 'pendiente',
            'seguro_estado' => $tieneSeguro ? 'pendiente_autorizacion' : null,
            'seguro_id' => $tieneSeguro ? $request->seguro_id : null,
        ]);
        
        // Crear detalle de la cuenta
        \App\Models\CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item' => 'servicio',
            'descripcion' => 'Consulta Externa - ' . $consulta->codigo,
            'cantidad' => 1,
            'precio_unitario' => $costoConsulta,
            'subtotal' => $costoConsulta,
        ]);
        
        return $cuenta;
    }

    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'tipo' => 'CONSULTA',
                'cobertura' => 'Sin cobertura',
                'telefono' => null,
                'formulario' => 'ESTANDAR',
                'estado' => 'activo'
            ]
        );
        
        return $seguro->id;
    }

    private function obenerOCrearTriage()
    {
        $currentUser = Auth::user();
        
        $triage = Triage::firstOrCreate(
            ['id' => 'TRIAGE-CONSULTA'],
            [
                'color' => 'green',
                'descripcion' => 'Consulta Externa - No Urgente',
                'prioridad' => 'baja',
                'user_id' => $currentUser->id
            ]
        );
        
        return $triage->id;
    }

    private function obtenerOCrearRegistro()
    {
        $currentUser = Auth::user();
        $codigo = 'REG-' . date('Y') . '-' . str_pad(Registro::count() + 1, 6, '0', STR_PAD_LEFT);
        
        $registro = Registro::firstOrCreate(
            ['codigo' => $codigo],
            [
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => 'Registro de Consulta Externa',
                'user_id' => $currentUser->id
            ]
        );
        
        return $registro->codigo;
    }

    private function obtenerMedicoId($medicoSeleccionado)
    {
        // Si viene un CI numérico directo, usarlo
        if (is_numeric($medicoSeleccionado)) {
            return (int) $medicoSeleccionado;
        }

        // Si viene formato dr_nombre, buscar el primer médico activo disponible
        // basado en la especialidad o cualquier médico activo
        $medico = Medico::where('estado', 'activo')->first();

        return $medico ? $medico->ci : null;
    }

    private function obtenerEspecialidadCodigo($especialidadCodigo)
    {
        $especialidad = Especialidad::where('codigo', $especialidadCodigo)->first();

        if ($especialidad) {
            return $especialidad->codigo;
        }

        $especialidadPorNombre = Especialidad::where('nombre', ucfirst(str_replace('_', ' ', $especialidadCodigo)))->first();

        return $especialidadPorNombre?->codigo;
    }

    public function procesarTriageGeneral(Request $request)
    {
        $request->validate([
            'ci' => 'required|string|min:6',
            'triage_tipo' => 'required|in:rojo,amarillo,verde',
        ]);

        try {
            DB::beginTransaction();

            $paciente = $this->crearOActualizarPaciente($request);
            $triage = $this->crearTriagePorTipo($request->triage_tipo);
            $paciente->update(['triage_id' => $triage->id]);

            if ($request->triage_tipo === 'verde') {
                $caja = $this->crearRegistroCajaPendiente($request, $paciente);
                $consulta = $this->crearConsulta($request, $paciente, $caja);
                
                // Crear cuenta por cobrar para caja
                $this->crearCuentaCobro($request, $paciente, $consulta, $caja);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'flujo' => 'consulta_normal',
                    'message' => 'Triage VERDE registrado. Continúe con caja y consulta externa.',
                    'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id]),
                    'consulta_id' => $consulta->id,
                ]);
            }

            if ($request->triage_tipo === 'rojo') {
                $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                DB::table('emergencias')->insert([
                    'nro' => $nroEmergencia,
                    'descripcion' => $request->motivo ?? 'Emergencia por triage rojo',
                    'estado' => 'INGRESADO',
                    'tipo' => strtoupper($request->input('tipo_emergencia', 'EMERGENCIA')),
                    'id_triage' => $triage->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $acciones = ['enviado_emergencia' => true];

                if ($request->boolean('accidente_automovilistico')) {
                    $this->obtenerOCrearRegistroMotivo('Formulario SOAT - ' . $paciente->ci);
                    $acciones['formulario_soat'] = true;
                }

                if ($request->boolean('requiere_cirugia_inmediata')) {
                    $acciones['traslado_uci'] = true;
                    $quirofano = DB::table('quirofanos')->orderBy('nro')->first();
                    if ($quirofano) {
                        $nroCirugia = 'CIR-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                        DB::table('cirugias')->insert([
                            'nro' => $nroCirugia,
                            'fecha' => now()->toDateString(),
                            'hora' => now()->toTimeString(),
                            'tipo' => 'CIRUGIA_INMEDIATA',
                            'descripcion' => $request->motivo ?? 'Cirugía inmediata por triage rojo',
                            'nro_emergencia' => $nroEmergencia,
                            'nro_quirofano' => $quirofano->nro,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $acciones['cirugia'] = true;
                    }
                } else {
                    $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                    DB::table('hospitalizaciones')->insert([
                        'id' => $idHosp,
                        'fecha_ingreso' => now(),
                        'motivo' => 'Observación posterior a triage rojo',
                        'nro_emergencia' => $nroEmergencia,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $acciones['internacion_uti'] = true;
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'flujo' => 'emergencia',
                    'message' => 'Triage ROJO procesado y derivado a emergencia.',
                    'acciones' => $acciones,
                ]);
            }

            // AMARILLO - Parto
            $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
            $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
            DB::table('emergencias')->insert([
                'nro' => $nroEmergencia,
                'descripcion' => 'Ingreso para parto',
                'estado' => 'INGRESADO',
                'tipo' => 'PARTO',
                'id_triage' => $triage->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('hospitalizaciones')->insert([
                'id' => $idHosp,
                'fecha_ingreso' => now(),
                'motivo' => $request->boolean('requiere_estabilizacion_previa') ? 'Estabilización previa a parto' : 'Ingreso a parto',
                'nro_emergencia' => $nroEmergencia,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $quirofano = DB::table('quirofanos')->orderBy('nro')->first();
            $acciones = [
                'registro_paciente' => true,
                'parto_quirofano_exclusivo' => true,
                'asignacion_neonatologia' => true,
            ];

            if ($quirofano) {
                $nroCirugia = 'CIR-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                DB::table('cirugias')->insert([
                    'nro' => $nroCirugia,
                    'fecha' => now()->toDateString(),
                    'hora' => now()->toTimeString(),
                    'tipo' => 'PARTO',
                    'descripcion' => 'Parto en quirófano exclusivo',
                    'nro_emergencia' => $nroEmergencia,
                    'nro_quirofano' => $quirofano->nro,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Inserción en tabla partos - envuelta en try/catch para no bloquear flujo principal
                try {
                    $columnasPartos = \Schema::hasTable('partos') ? \Schema::getColumnListing('partos') : [];
                    if (!empty($columnasPartos)) {
                        $dataParto = [
                            'nro' => 'PARTO-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                            'tipo' => 'INSTITUCIONAL',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        // Solo agregar campos si existen en la tabla
                        if (in_array('observaciones', $columnasPartos)) {
                            $dataParto['observaciones'] = $request->observaciones;
                        }
                        if (in_array('id_hospitalizacion', $columnasPartos)) {
                            $dataParto['id_hospitalizacion'] = $idHosp;
                        }
                        if (in_array('nro_cirugia', $columnasPartos)) {
                            $dataParto['nro_cirugia'] = $nroCirugia;
                        }
                        DB::table('partos')->insert($dataParto);
                    }
                } catch (\Exception $e) {
                    \Log::warning('No se pudo registrar parto: ' . $e->getMessage());
                    // No lanzar - no debe bloquear el flujo principal
                }
            }

            if ($request->boolean('requiere_observacion_postparto')) {
                $acciones['observacion_postparto_uti'] = true;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'flujo' => 'parto',
                'message' => 'Triage AMARILLO (parto) procesado correctamente.',
                'acciones' => $acciones,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar triage: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function crearTriagePorTipo(string $tipo)
    {
        $currentUser = Auth::user();

        $map = [
            'rojo' => ['color' => 'red', 'descripcion' => 'Emergencia', 'prioridad' => 'alta'],
            'amarillo' => ['color' => 'yellow', 'descripcion' => 'Parto', 'prioridad' => 'media'],
            'verde' => ['color' => 'green', 'descripcion' => 'Consulta Externa - No Urgente', 'prioridad' => 'baja'],
        ];

        $cfg = $map[$tipo];
        return Triage::create([
            'id' => 'TRIAGE-' . strtoupper($tipo) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color' => $cfg['color'],
            'descripcion' => $cfg['descripcion'],
            'prioridad' => $cfg['prioridad'],
            'user_id' => $currentUser->id,
        ]);
    }

    private function obtenerOCrearRegistroMotivo(string $motivo)
    {
        return Registro::firstOrCreate(
            ['codigo' => 'REG-' . now()->format('YmdHis') . '-' . random_int(100, 999)],
            [
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => $motivo,
                'user_id' => Auth::id(),
            ]
        );
    }

    private function obtenerPrecioConsultaExterna(): float
    {
        $precioNuevo = \App\Models\IngresoPrecio::getPrecio('consulta_externa');

        if ($precioNuevo !== null) {
            return (float) $precioNuevo;
        }

        $servicio = \App\Models\Servicio::getServicioPorTipo('CONSULTA_EXTERNA');

        return $servicio ? (float) $servicio->precio : 50.00;
    }
}
