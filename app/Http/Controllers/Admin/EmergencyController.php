<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmergencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|dirmedico')->only(['index', 'show']);
        $this->middleware('role:admin|dirmedico')->only(['create', 'store']);
        $this->middleware('role:admin|dirmedico')->only(['edit', 'update']);
        $this->middleware('role:admin')->only(['destroy']);
    }

    public function index(): View
    {
        $emergencies = Emergency::with(['paciente', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Emergency::count(),
            'active' => Emergency::whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'uti' => Emergency::where('status', 'uti')->count(),
            'surgery' => Emergency::where('status', 'cirugia')->count(),
            'discharged' => Emergency::where('status', 'alta')->count(),
        ];

        return view('admin.emergencies.index', compact('emergencies', 'stats'));
    }

    public function create(): View
    {
        $pacientes = Paciente::orderBy('name')->get();
        $users = User::role('emergency')->orderBy('name')->get();
        
        return view('admin.emergencies.create', compact('pacientes', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:pacientes,id',
            'user_id' => 'required|exists:users,id',
            'symptoms' => 'required|string',
            'initial_assessment' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'treatment' => 'nullable|string',
            'observations' => 'nullable|string',
            'destination' => 'nullable|in:observacion,uti,cirugia,consulta_externa,alta',
            'cost' => 'required|numeric|min:0',
        ]);

        $validated['code'] = Emergency::generateCode();
        $validated['status'] = 'recibido';
        $validated['admission_date'] = now();

        Emergency::create($validated);

        return redirect()->route('admin.emergencies.index')
            ->with('success', 'Emergencia creada exitosamente.');
    }

    public function show(Emergency $emergency): View
    {
        $emergency->load(['paciente', 'user']);
        
        return view('admin.emergencies.show', compact('emergency'));
    }

    public function edit(Emergency $emergency): View
    {
        $pacientes = Paciente::orderBy('name')->get();
        $users = User::role('emergency')->orderBy('name')->get();
        
        return view('admin.emergencies.edit', compact('emergency', 'pacientes', 'users'));
    }

    public function update(Request $request, Emergency $emergency): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:recibido,en_evaluacion,estabilizado,uti,cirugia,alta,fallecido',
            'symptoms' => 'required|string',
            'initial_assessment' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'treatment' => 'nullable|string',
            'observations' => 'nullable|string',
            'destination' => 'nullable|in:observacion,uti,cirugia,consulta_externa,alta',
            'cost' => 'required|numeric|min:0',
        ]);

        $emergency->update($validated);

        if ($emergency->status === 'alta' && !$emergency->discharge_date) {
            $emergency->update(['discharge_date' => now()]);
        }

        return redirect()->route('admin.emergencies.index')
            ->with('success', 'Emergencia actualizada exitosamente.');
    }

    public function destroy(Emergency $emergency): RedirectResponse
    {
        $emergency->delete();

        return redirect()->route('admin.emergencies.index')
            ->with('success', 'Emergencia eliminada exitosamente.');
    }

    public function markAsPaid(Emergency $emergency): RedirectResponse
    {
        $emergency->update(['paid' => true]);

        return redirect()->back()
            ->with('success', 'Emergencia marcada como pagada.');
    }
}
