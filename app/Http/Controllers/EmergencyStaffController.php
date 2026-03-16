<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Patient;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class EmergencyStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:emergencies.view');
        $this->middleware('permission:emergencies.create')->only(['create', 'store']);
        $this->middleware('permission:emergencies.update')->only(['update', 'updateStatus']);
    }

    public function index(): View
    {
        $emergencies = Emergency::with(['patient'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pendingCount = Emergency::where('status', 'recibido')->count();
        $myActiveCount = Emergency::where('user_id', auth()->id())
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
            ->count();

        return view('emergency-staff.dashboard', compact('emergencies', 'pendingCount', 'myActiveCount'));
    }

    public function create(): View
    {
        $patients = Patient::orderBy('name')->get();
        
        return view('emergency-staff.create', compact('patients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'symptoms' => 'required|string',
            'initial_assessment' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
        ]);

        $validated['code'] = Emergency::generateCode();
        $validated['status'] = 'recibido';
        $validated['user_id'] = auth()->id();
        $validated['admission_date'] = now();

        Emergency::create($validated);

        return redirect()->route('emergency-staff.dashboard')
            ->with('success', 'Paciente recibido en emergencias.');
    }

    public function show(Emergency $emergency): View
    {
        if ($emergency->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $emergency->load(['patient']);
        
        return view('emergency-staff.show', compact('emergency'));
    }

    public function edit(Emergency $emergency): View
    {
        if ($emergency->user_id !== auth()->id()) {
            abort(403);
        }

        return view('emergency-staff.edit', compact('emergency'));
    }

    public function update(Request $request, Emergency $emergency): RedirectResponse
    {
        if ($emergency->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:recibido,en_evaluacion,estabilizado,uti,cirugia,alta,fallecido',
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

        return redirect()->route('emergency-staff.dashboard')
            ->with('success', 'Emergencia actualizada exitosamente.');
    }

    public function updateStatus(Request $request, Emergency $emergency): JsonResponse
    {
        if ($emergency->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:recibido,en_evaluacion,estabilizado,uti,cirugia,alta,fallecido',
        ]);

        $emergency->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'status' => $emergency->status,
            'status_color' => $emergency->status_color,
        ]);
    }

    public function assignToMe(Emergency $emergency): RedirectResponse
    {
        if ($emergency->user_id) {
            return redirect()->back()
                ->with('error', 'Esta emergencia ya está asignada.');
        }

        $emergency->update([
            'user_id' => auth()->id(),
            'status' => 'en_evaluacion'
        ]);

        return redirect()->route('emergency-staff.show', $emergency)
            ->with('success', 'Emergencia asignada a ti.');
    }

    public function pending(): View
    {
        $emergencies = Emergency::with(['patient'])
            ->whereNull('user_id')
            ->orWhere('status', 'recibido')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('emergency-staff.pending', compact('emergencies'));
    }
}
