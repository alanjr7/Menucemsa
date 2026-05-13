<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Hospitalizacion;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\Seguro;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Services\CuentaCobroService;
use App\Services\EpisodioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HospitalizacionController extends Controller
{
    public function index()
    {
        $seguros = Seguro::where('estado', 'activo')
            ->orderBy('nombre_empresa')
            ->get();

        return view('reception.hospitalizacion', compact('seguros'));
    }

    public function buscarPaciente(Request $request)
    {
        $ci = $request->get('ci');

        if (empty($ci) || strlen($ci) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe tener al menos 3 caracteres'
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

    public function registrarHospitalizacion(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Crear o encontrar paciente
            $paciente = $this->crearOActualizarPaciente($request);

            // 2. Verificar si ya tiene una hospitalización activa
            $hospitalizacionActiva = Hospitalizacion::where('ci_paciente', $paciente->ci)
                ->whereNull('fecha_alta')
                ->first();

            if ($hospitalizacionActiva) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene una hospitalización activa. Código: ' . $hospitalizacionActiva->id,
                ], 422);
            }

            // 3. Crear triage (generalmente amarillo para hospitalización)
            $triage = $this->crearTriagePorTipo('amarillo');
            $paciente->update(['triage_id' => $triage->id]);

            // 4. Abrir episodio
            $episodio = EpisodioService::abrirEpisodio($paciente->ci, 'internacion', Auth::id());

            // 5. Crear registro de hospitalización
            $hospitalizacion = $this->crearHospitalizacion($request, $paciente, $triage, $episodio->id);

            // 6. Obtener o crear cuenta maestra (reutiliza la de emergencia si ya existe)
            $cuentaCobro = CuentaCobroService::obtenerOCrearCuentaMaestra(
                $paciente->ci,
                'internacion',
                $request->seguro_id
            );

            // Agregar cargo de admisión de internación (con deduplicación)
            $precioAdmision = self::obtenerPrecioInternacion();

            CuentaCobroService::agregarCargoConDeduplicacion(
                $cuentaCobro->id,
                'servicio',
                'Admisión de Internación',
                $precioAdmision,
                1,
                'internacion',
                \App\Models\Hospitalizacion::class,
                $hospitalizacion->id,
                null
            );

            $cuentaCobro->update(['episodio_id' => $episodio->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hospitalización registrada exitosamente',
                'hospitalizacion_id' => $hospitalizacion->id,
                'redirect_url' => route('reception.hospitalizacion.comprobante', $hospitalizacion->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al registrar hospitalización: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la hospitalización. Intente nuevamente.',
            ], 500);
        }
    }


    public function getHospitalizacionesActivas()
    {
        try {
            $hospitalizaciones = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])
                ->whereNull('fecha_alta')
                ->orderBy('fecha_ingreso', 'desc')
                ->get();

            // Formatear datos para la vista
            $hospitalizacionesFormateadas = $hospitalizaciones->map(function($hospitalizacion) {
                return [
                    'codigo' => $hospitalizacion->id,
                    'paciente' => [
                        'nombre' => $hospitalizacion->paciente?->nombre ?? 'Sin paciente',
                        'ci' => $hospitalizacion->paciente?->ci ?? 'N/A',
                    ],
                    'tipo' => 'hospitalizacion',
                    'servicio' => 'Hospitalización',
                    'motivo' => $hospitalizacion->motivo,
                    'fecha_ingreso' => $hospitalizacion->fecha_ingreso->format('d/m/Y'),
                    'medico' => [
                        'usuario' => [
                            'name' => $hospitalizacion->medico?->user?->name ?? 'No asignado',
                        ]
                    ],
                    'habitacion' => $hospitalizacion->habitacion_id ?? 'Por asignar',
                ];
            });

            return response()->json([
                'success' => true,
                'hospitalizaciones' => $hospitalizacionesFormateadas
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al cargar hospitalizaciones activas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar hospitalizaciones activas.',
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

            // Crear nuevo paciente
            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => trim($request->nombres . ' ' . $request->apellidos),
                'sexo' => $request->sexo === 'Femenino' ? 'F' : 'M',
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ?? 0,
                'correo' => $request->correo ?? 'sin@email.com',
                'seguro_id' => $request->seguro_id ?? $this->obtenerOCrearSeguro('particular'),
                'id_triage' => null, // Se asignará después
                'registro_codigo' => $this->obtenerOCrearRegistro([
                    'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
                    'sexo'             => $request->sexo === 'Femenino' ? 'F' : 'M',
                    'nombre'           => trim($request->nombres . ' ' . $request->apellidos),
                ]),
            ]);
        } else {
            // Actualizar datos si es necesario
            $updateData = [
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'direccion' => $request->direccion ?? $paciente->direccion,
            ];

            // Actualizar seguro solo si se envió explicitamente
            if ($request->has('seguro_id') && $request->seguro_id !== null) {
                $updateData['seguro_id'] = $request->seguro_id;
            }

            $paciente->update($updateData);
        }
        
        return $paciente;
    }

    private function crearHospitalizacion($request, $paciente, $triage, ?int $episodioId = null)
    {
        $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);

        return Hospitalizacion::create([
            'id' => $idHosp,
            'fecha_ingreso' => now(),
            'motivo' => $request->motivo,
            'diagnostico' => $request->diagnostico,
            'estado' => 'activo',
            'ci_medico' => $request->medico_tratante,
            'ci_paciente' => $paciente->ci,
            'contacto_nombre' => $request->contacto_nombre,
            'contacto_telefono' => $request->contacto_telefono,
            'contacto_parentesco' => $request->contacto_parentesco,
            'contacto_relacion' => $request->contacto_relacion,
            'episodio_id' => $episodioId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearTriagePorTipo(string $tipo)
    {
        $currentUser = Auth::user();

        $map = [
            'rojo' => ['color' => 'red', 'descripcion' => 'Hospitalización - Emergencia', 'prioridad' => 'alta'],
            'amarillo' => ['color' => 'yellow', 'descripcion' => 'Hospitalización - Urgencia', 'prioridad' => 'media'],
            'verde' => ['color' => 'green', 'descripcion' => 'Hospitalización - Observación', 'prioridad' => 'baja'],
        ];

        $cfg = $map[$tipo] ?? $map['amarillo'];
        return Triage::create([
            'id' => 'TRIAGE-' . strtoupper($tipo) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color' => $cfg['color'],
            'descripcion' => $cfg['descripcion'],
            'prioridad' => $cfg['prioridad'],
            'user_id' => $currentUser->id,
        ]);
    }

    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'tipo' => 'HOSPITALIZACION',
                'cobertura' => 'Sin cobertura',
                'telefono' => null,
                'formulario' => 'HOSPITALIZACION',
                'estado' => 'activo'
            ]
        );
        
        return $seguro->id;
    }

    private function obtenerOCrearRegistro(array $datosPaciente = []): string
    {
        $codigo = Registro::generarCodigo($datosPaciente);

        Registro::firstOrCreate(
            ['codigo' => $codigo],
            [
                'fecha'   => now()->toDateString(),
                'hora'    => now()->toTimeString(),
                'motivo'  => 'Registro de Hospitalización',
                'user_id' => Auth::id(),
            ]
        );

        return $codigo;
    }

    public function darAlta(Request $request, $id)
    {
        try {
            $hospitalizacion = Hospitalizacion::findOrFail($id);
            
            if ($hospitalizacion->fecha_alta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta hospitalización ya fue dada de alta'
                ], 422);
            }

            $hospitalizacion->update([
                'fecha_alta' => now(),
                'motivo_alta' => $request->motivo_alta ?? 'Alta médica',
                'updated_at' => now(),
            ]);

            if ($hospitalizacion->ci_paciente) {
                EpisodioService::cerrarEpisodioDelPaciente(
                    $hospitalizacion->ci_paciente,
                    Auth::id(),
                    $request->motivo_alta ?? 'alta_medica'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Paciente dado de alta exitosamente',
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al dar de alta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al dar de alta. Intente nuevamente.',
            ], 500);
        }
    }

    public function actualizarDatos(Request $request, $id)
    {
        try {
            $hospitalizacion = Hospitalizacion::findOrFail($id);
            
            $hospitalizacion->update([
                'motivo' => $request->motivo ?? $hospitalizacion->motivo,
                'diagnostico' => $request->diagnostico ?? $hospitalizacion->diagnostico,
                'contacto_nombre' => $request->contacto_nombre ?? $hospitalizacion->contacto_nombre,
                'contacto_telefono' => $request->contacto_telefono ?? $hospitalizacion->contacto_telefono,
                'contacto_parentesco' => $request->contacto_parentesco ?? $hospitalizacion->contacto_parentesco,
                'contacto_relacion' => $request->contacto_relacion ?? $hospitalizacion->contacto_relacion,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos de hospitalización actualizados exitosamente',
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar datos de hospitalización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar datos. Intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Mostrar comprobante de hospitalización
     */
    public function comprobante($id)
    {
        $hospitalizacion = Hospitalizacion::with(['paciente', 'medico.user'])->findOrFail($id);

        // Obtener el triage del paciente (si tiene)
        $triage = $hospitalizacion->paciente->triage ?? null;

        return view('reception.hospitalizacion-comprobante', compact('hospitalizacion', 'triage'));
    }

    private function obtenerPrecioInternacion(): float
    {
        $precioNuevo = \App\Models\IngresoPrecio::getPrecio('internacion');

        if ($precioNuevo !== null) {
            return (float) $precioNuevo;
        }

        $tarifaInternacion = \App\Models\Tarifa::where('codigo', 'HOSP-ADM')
            ->where('activo', true)
            ->first();

        return $tarifaInternacion?->precio_particular ?? 150.00;
    }
}
