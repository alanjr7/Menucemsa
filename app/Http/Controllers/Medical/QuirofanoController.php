<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\QuirofanoCita;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QuirofanoController extends Controller
{
    public function index()
    {
        $citas = QuirofanoCita::query()
            ->orderBy('scheduled_at')
            ->limit(20)
            ->get();

        return view('medical.quirofano', compact('citas'));
    }

    public function create()
    {
        return view('medical.quirofano-create');
    }

    public function store(Request $request)
    {
        $data = $this->normalizePayload($request);

        $validated = validator($data, [
            'patient_name' => ['required', 'string', 'max:255'],
            'procedure_name' => ['required', 'string', 'max:255'],
            'surgeon_name' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'operating_room' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ])->validate();

        $validated['scheduled_at'] = Carbon::parse($validated['scheduled_at']);

        $cita = QuirofanoCita::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cita de quirófano creada correctamente.',
                'data' => $cita,
            ], 201);
        }

        return redirect()
            ->route('quirofano.index')
            ->with('status', 'Cita de quirófano creada correctamente.');
    }

    private function normalizePayload(Request $request): array
    {
        $input = $request->all();

        $scheduledAt = $this->firstNotEmpty($input, [
            'scheduled_at',
            'fecha_hora',
            'fechaHora',
            'fecha_programada',
            'fecha',
            'datetime',
            'start',
        ]);

        if (!$scheduledAt) {
            $datePart = $this->firstNotEmpty($input, ['scheduled_date', 'fecha', 'date']);
            $timePart = $this->firstNotEmpty($input, ['scheduled_time', 'hora', 'time']);
            if ($datePart && $timePart) {
                $scheduledAt = "$datePart $timePart";
            }
        }

        return [
            'patient_name' => $this->firstNotEmpty($input, ['patient_name', 'paciente', 'patient', 'nombre_paciente']),
            'procedure_name' => $this->firstNotEmpty($input, ['procedure_name', 'procedimiento', 'cirugia', 'nombre_procedimiento']),
            'surgeon_name' => $this->firstNotEmpty($input, ['surgeon_name', 'cirujano', 'medico', 'doctor']),
            'scheduled_at' => $scheduledAt,
            'operating_room' => $this->firstNotEmpty($input, ['operating_room', 'sala', 'quirofano', 'room']),
            'notes' => $this->firstNotEmpty($input, ['notes', 'nota', 'observaciones', 'detalle']),
        ];
    }

    private function firstNotEmpty(array $input, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $input)) {
                continue;
            }

            $value = is_string($input[$key]) ? trim($input[$key]) : $input[$key];
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
