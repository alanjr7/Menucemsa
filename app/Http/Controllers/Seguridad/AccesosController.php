<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\AllowedIp;
use App\Models\IpAccessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccesosController extends Controller
{
    public function index()
    {
        $setting = IpAccessSetting::first();
        $allowedIps = AllowedIp::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
        $currentIp = request()->ip();

        return view('seguridad.accesos.index', compact('setting', 'allowedIps', 'currentIp'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|string|max:45',
            'ip_type' => 'required|in:single,range',
            'description' => 'nullable|string|max:255',
        ]);

        if (!AllowedIp::validateIpFormat($validated['ip_address'], $validated['ip_type'])) {
            return redirect()->back()
                ->with('error', 'El formato de la IP no es válido. Para rangos, use el formato CIDR (ej: 192.168.1.0/24)');
        }

        $existingIp = AllowedIp::where('ip_address', $validated['ip_address'])
            ->where('is_active', true)
            ->first();

        if ($existingIp) {
            return redirect()->back()
                ->with('error', 'Esta IP o rango ya está registrado en la lista de permitidos.');
        }

        AllowedIp::create([
            'ip_address' => $validated['ip_address'],
            'ip_type' => $validated['ip_type'],
            'description' => $validated['description'],
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('seguridad.accesos.index')
            ->with('success', 'IP agregada correctamente a la lista de permitidos.');
    }

    public function destroy(AllowedIp $acceso)
    {
        $acceso->delete();

        return redirect()->route('seguridad.accesos.index')
            ->with('success', 'IP eliminada correctamente de la lista de permitidos.');
    }

    public function updateMode(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|in:all,specific',
        ]);

        IpAccessSetting::setMode($validated['mode']);

        $message = $validated['mode'] === 'all'
            ? 'El sistema ahora permite acceso desde cualquier IP.'
            : 'El sistema ahora solo permite acceso desde las IPs especificadas.';

        return redirect()->route('seguridad.accesos.index')
            ->with('success', $message);
    }
}
