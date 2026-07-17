<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Support\BuscadorCatalogo;
use App\Support\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Proformas (cotizaciones / presupuestos).
 *
 * Accesible a TODOS los roles autenticados: cualquier usuario crea proformas y
 * ve las suyas. Admin y administrador ven las de todos (Proforma::esGestor).
 * No es un cargo real: no toca caja, cuentas ni contabilidad.
 */
class ProformaController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Proforma::with('user')->visiblesPara($user);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('numero', 'LIKE', "%{$s}%")
                  ->orWhere('paciente_nombre', 'LIKE', "%{$s}%")
                  ->orWhere('paciente_documento', 'LIKE', "%{$s}%");
            });
        }

        $proformas = $query->latest()->paginate(12)->withQueryString();

        return view('proformas.index', [
            'proformas' => $proformas,
            'esGestor'  => Proforma::esGestor($user),
        ]);
    }

    public function create(): View
    {
        return view('proformas.create', ['proforma' => new Proforma()]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $proforma = DB::transaction(function () use ($datos) {
            $proforma = Proforma::create([
                'paciente_nombre'     => $datos['paciente_nombre'],
                'paciente_documento'  => $datos['paciente_documento'] ?? null,
                'paciente_telefono'   => $datos['paciente_telefono'] ?? null,
                'validez_dias'        => $datos['validez_dias'],
                'descuento'           => $datos['descuento'] ?? 0,
                'observaciones'       => $datos['observaciones'] ?? null,
                'total'               => 0,
                'user_id'             => Auth::id(),
            ]);

            $this->sincronizarItems($proforma, $datos['items'], $datos['descuento'] ?? 0);

            return $proforma;
        });

        return redirect()->route('proformas.show', $proforma)
            ->with('success', "Proforma {$proforma->numero} creada correctamente.");
    }

    public function show(Proforma $proforma): View
    {
        $this->autorizar($proforma);
        $proforma->load('items', 'user');

        return view('proformas.show', compact('proforma'));
    }

    public function edit(Proforma $proforma): View
    {
        $this->autorizar($proforma);
        $proforma->load('items');

        return view('proformas.edit', compact('proforma'));
    }

    public function update(Request $request, Proforma $proforma)
    {
        $this->autorizar($proforma);

        $datos = $this->validar($request);

        DB::transaction(function () use ($proforma, $datos) {
            $proforma->update([
                'paciente_nombre'     => $datos['paciente_nombre'],
                'paciente_documento'  => $datos['paciente_documento'] ?? null,
                'paciente_telefono'   => $datos['paciente_telefono'] ?? null,
                'validez_dias'        => $datos['validez_dias'],
                'descuento'           => $datos['descuento'] ?? 0,
                'observaciones'       => $datos['observaciones'] ?? null,
            ]);

            // Reemplazo total de líneas: simple y correcto para un documento
            // editable que se regenera completo en cada guardado.
            $proforma->items()->delete();
            $this->sincronizarItems($proforma, $datos['items'], $datos['descuento'] ?? 0);
        });

        return redirect()->route('proformas.show', $proforma)
            ->with('success', "Proforma {$proforma->numero} actualizada correctamente.");
    }

    public function destroy(Proforma $proforma)
    {
        $this->autorizar($proforma);
        $numero = $proforma->numero;
        $proforma->delete();

        return redirect()->route('proformas.index')
            ->with('success', "Proforma {$numero} eliminada.");
    }

    /**
     * Documento imprimible (formato profesional, ventana independiente).
     */
    public function imprimir(Proforma $proforma): View
    {
        $this->autorizar($proforma);
        $proforma->load('items', 'user');

        return view('proformas.imprimir', compact('proforma'));
    }

    /**
     * Autocompletado de ítems del catálogo (compartido con Correcciones).
     */
    public function buscarCatalogo(Request $request): JsonResponse
    {
        return response()->json(
            BuscadorCatalogo::buscar((string) $request->get('q', ''))
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function validar(Request $request): array
    {
        return $request->validate([
            'paciente_nombre'         => 'required|string|max:255',
            'paciente_documento'      => 'nullable|string|max:50',
            'paciente_telefono'       => 'nullable|string|max:50',
            'validez_dias'            => 'required|integer|min:1|max:365',
            'descuento'               => Money::rules(false),
            'observaciones'           => 'nullable|string|max:2000',
            'items'                   => 'required|array|min:1',
            'items.*.descripcion'     => 'required|string|max:255',
            'items.*.cantidad'        => 'required|numeric|decimal:0,2|min:0.01',
            'items.*.precio_unitario' => Money::rules(true),
            'items.*.codigo_item'     => 'nullable|string|max:12',
            'items.*.tipo_item'       => 'nullable|string|max:30',
        ], [], [
            'paciente_nombre'         => 'nombre del paciente',
            'items'                   => 'detalle',
            'items.*.descripcion'     => 'concepto',
            'items.*.precio_unitario' => 'precio unitario',
        ]);
    }

    /**
     * Crea las líneas de la proforma calculando subtotales con BCMath y
     * actualiza el total (sum de subtotales − descuento, nunca negativo).
     */
    private function sincronizarItems(Proforma $proforma, array $items, $descuento): void
    {
        $totalBruto = '0';
        $orden = 0;

        foreach ($items as $item) {
            $subtotal = Money::mul($item['cantidad'], $item['precio_unitario']);
            $totalBruto = Money::add($totalBruto, $subtotal);

            $proforma->items()->create([
                'codigo_item'     => filled($item['codigo_item'] ?? null) ? trim($item['codigo_item']) : null,
                'tipo_item'       => $item['tipo_item'] ?? null,
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $subtotal,
                'orden'           => $orden++,
            ]);
        }

        $total = Money::clampZero(Money::sub($totalBruto, $descuento ?: 0));

        $proforma->update(['total' => $total]);
    }

    /**
     * 403 si el usuario no puede operar sobre la proforma (no es suya ni es gestor).
     */
    private function autorizar(Proforma $proforma): void
    {
        abort_unless($proforma->puedeVerla(Auth::user()), 403);
    }
}
