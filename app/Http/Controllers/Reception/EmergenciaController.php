<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Emergency;
use App\Models\Triage;
use App\Models\Registro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmergenciaController extends Controller
{
    public function index()
    {
        return view('reception.emergencia');
    }

    public function buscarPaciente(Request $request)
    {
        $ci = $request->get('ci');
        
        if (strlen($ci) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe tener al menos 8 dígitos'
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

    public function registrarEmergencia(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Crear o encontrar paciente
            $paciente = $this->crearOActualizarPaciente($request);

            // 2. Crear triage de emergencia
            $triage = $this->crearTriagePorTipo($request->tipo_emergencia ?? 'amarillo');
            $paciente->update(['id_triage' => $triage->id]);

            // 3. Crear registro de emergencia usando modelo Emergency
            $emergencia = $this->crearEmergency($request, $paciente, $triage);

            // 4. Procesar acciones adicionales según el nivel
            $acciones = $this->procesarAccionesEmergencia($request, $paciente, $emergencia, $triage);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Emergencia registrada exitosamente',
                'emergency_code' => $emergencia->nro,
                'emergencia' => $emergencia->load(['paciente']),
                'acciones' => $acciones
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la emergencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmergenciasActivas()
    {
        try {
            $emergencias = Emergency::with(['paciente'])
                ->where('estado', 'INGRESADO')
                ->orderBy('created_at', 'desc')
                ->get();

            // Formatear datos para la vista
            $emergenciasFormateadas = $emergencias->map(function($emergencia) {
                return [
                    'codigo' => $emergencia->nro,
                    'paciente' => [
                        'nombre' => $emergencia->paciente->nombre,
                        'ci' => $emergencia->paciente->ci
                    ],
                    'nivel' => strtolower($emergencia->status ?? 'yellow'),
                    'tipo_emergencia' => $emergencia->tipo,
                    'descripcion' => $emergencia->descripcion,
                    'hora_ingreso' => $emergencia->created_at->format('H:i'),
                    'estado' => $emergencia->estado
                ];
            });

            return response()->json([
                'success' => true,
                'emergencias' => $emergenciasFormateadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar emergencias activas: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearOActualizarPaciente($request)
    {
        $ci = $request->ci;
        $usarTempId = $request->boolean('usar_temp_id');
        
        if ($usarTempId) {
            // Crear paciente con ID temporal y datos mínimos
            $tempId = $request->temp_id ?? 'TEMP-' . now()->format('Ymd') . '-' . random_int(100, 999);
            
            return Paciente::create([
                'ci' => $tempId,
                'nombre' => 'Paciente Temporal - Emergencia',
                'sexo' => 'No especificado',
                'direccion' => 'Por especificar - ID Temporal',
                'telefono' => 0,
                'correo' => 'temporal@emergencia.com',
                'seguro_id' => $this->obtenerOCrearSeguro('particular'),
                'id_triage' => null, // Se asignará después
                'registro_codigo' => $this->obtenerOCrearRegistro(),
            ]);
        }
        
        // Buscar paciente existente
        $paciente = Paciente::find($ci);
        
        if (!$paciente) {
            // Validar que todos los campos requeridos estén presentes
            $request->validate([
                'nombres' => 'required|string|max:80',
                'apellidos' => 'required|string|max:80',
                'sexo' => 'required|string|in:Masculino,Femenino',
            ]);

            // Crear nuevo paciente
            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => trim($request->nombres . ' ' . $request->apellidos),
                'sexo' => $request->sexo === 'Femenino' ? 'F' : 'M',
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ?? 0,
                'correo' => $request->correo ?? 'sin@email.com',
                'seguro_id' => $this->obtenerOCrearSeguro($request->seguro ?? 'particular'),
                'id_triage' => null, // Se asignará después
                'registro_codigo' => $this->obtenerOCrearRegistro(),
            ]);
        } else {
            // Actualizar datos si es necesario
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'direccion' => $request->direccion ?? $paciente->direccion,
            ]);
        }
        
        return $paciente;
    }

    private function crearEmergency($request, $paciente, $triage)
    {
        $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        
        return Emergency::create([
            'user_id' => Auth::user()->id,
            'code' => $nroEmergencia,
            'status' => 'recibido',
            'tipo_ingreso' => strtoupper($request->tipo_emergencia) === 'SOAT' ? 'soat' : 'general',
            'symptoms' => $request->descripcion,
            'initial_assessment' => $request->motivo,
            'is_temp_id' => $usarTempId,
            'temp_id' => $usarTempId ? $tempId : null,
            'ubicacion_actual' => 'emergencia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function procesarAccionesEmergencia($request, $paciente, $emergencia, $triage)
    {
        $acciones = [];

        // Acción principal: enviar a emergencia
        $acciones['enviado_emergencia'] = true;

        // Accidente automovilístico
        if ($request->boolean('accidente_automovilistico')) {
            $this->obtenerOCrearRegistroMotivo('Formulario SOAT - ' . $paciente->ci);
            $acciones['formulario_soat'] = true;
        }

        // Cirugía inmediata
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
                    'descripcion' => $request->descripcion,
                    'nro_emergencia' => $emergencia->code,
                    'nro_quirofano' => $quirofano->nro,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $acciones['cirugia'] = true;
            }
        }

        // Requiere UCI
        if ($request->boolean('requiere_uci')) {
            $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
            DB::table('hospitalizaciones')->insert([
                'id' => $idHosp,
                'fecha_ingreso' => now(),
                'motivo' => 'Hospitalización en UCI por emergencia',
                'nro_emergencia' => $emergencia->nro,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $acciones['internacion_uti'] = true;
        }

        // Contactar familiar
        if ($request->boolean('contacto_familia')) {
            $this->obtenerOCrearRegistroMotivo('Contacto familiar notificado - ' . $paciente->ci);
            $acciones['contacto_familiar'] = true;
        }

        return $acciones;
    }

    private function crearTriagePorTipo(string $tipo)
    {
        $currentUser = Auth::user();

        // Mapeo de tipos de emergencia a niveles de triage
        $emergenciaToTriage = [
            'trauma' => 'rojo',
            'accidente' => 'rojo', 
            'cardiaco' => 'rojo',
            'respiratorio' => 'naranja',
            'neurologico' => 'naranja',
            'obstetrico' => 'naranja',
            'pediatrico' => 'amarillo',
            'quemadura' => 'amarillo',
            'intoxicacion' => 'amarillo',
            'otro' => 'amarillo'
        ];

        // Determinar el nivel de triage basado en el tipo de emergencia
        $nivelTriage = $emergenciaToTriage[$tipo] ?? 'amarillo';

        $map = [
            'rojo' => ['color' => 'red', 'descripcion' => 'Emergencia Inmediata', 'prioridad' => 'alta'],
            'naranja' => ['color' => 'orange', 'descripcion' => 'Emergencia Urgente', 'prioridad' => 'alta'],
            'amarillo' => ['color' => 'yellow', 'descripcion' => 'Emergencia Media', 'prioridad' => 'media'],
            'verde' => ['color' => 'green', 'descripcion' => 'Emergencia Baja', 'prioridad' => 'baja'],
        ];

        $cfg = $map[$nivelTriage] ?? $map['amarillo'];
        return Triage::create([
            'id' => 'TRIAGE-' . strtoupper($nivelTriage) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color' => $cfg['color'],
            'descripcion' => $cfg['descripcion'],
            'prioridad' => $cfg['prioridad'],
            'user_id' => $currentUser->id,
        ]);
    }

    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = \App\Models\Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'tipo' => 'EMERGENCIA',
                'cobertura' => 'Cobertura de Emergencia',
                'telefono' => null,
                'formulario' => 'EMERGENCIA',
                'estado' => 'activo'
            ]
        );
        
        return $seguro->id;
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
                'motivo' => 'Registro de Emergencia',
                'user_id' => $currentUser->id
            ]
        );
        
        return $registro->codigo;
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

    public function actualizarEstadoEmergencia(Request $request, $nroEmergencia)
    {
        try {
            $emergencia = Emergency::where('code', $nroEmergencia)->firstOrFail();
            
            $emergencia->update([
                'status' => $this->mapEstadoToStatus($request->estado),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de emergencia actualizado exitosamente',
                'emergencia' => $emergencia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    private function mapEstadoToStatus(string $estado): string
    {
        $map = [
            'INGRESADO' => 'recibido',
            'ATENDIDO' => 'estabilizado',
            'ALTA' => 'alta',
            'DERIVADO' => 'estabilizado',
        ];
        return $map[$estado] ?? 'recibido';
    }
}
