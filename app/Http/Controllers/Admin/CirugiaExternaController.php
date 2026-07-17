<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CirugiaExterna;
use App\Models\TipoCirugiaExterna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Panel de administración de cirugías externas: listar, ver el recibo y
 * verificar/rechazar el pago (prepago con recibo). No toca caja/stock interno.
 * Acceso restringido a admin|administrador por middleware de ruta.
 */
class CirugiaExternaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'buscar' => trim((string) $request->query('buscar', '')),
            'estado' => (string) $request->query('estado', ''),
            'desde' => (string) $request->query('desde', ''),
            'hasta' => (string) $request->query('hasta', ''),
        ];

        // Tabla de Reservas: más recientes arriba, paginada de a 6, con búsqueda/filtros.
        $cirugias = CirugiaExterna::with(['tipo', 'quirofano'])
            ->when($filtros['buscar'] !== '', function ($q) use ($filtros) {
                $t = $filtros['buscar'];
                $q->where(function ($w) use ($t) {
                    $w->where('codigo', 'like', "%{$t}%")
                        ->orWhere('cirujano_nombre', 'like', "%{$t}%")
                        ->orWhere('paciente_nombre', 'like', "%{$t}%")
                        ->orWhere('cirujano_email', 'like', "%{$t}%")
                        ->orWhere('cirujano_telefono', 'like', "%{$t}%");
                });
            })
            ->when(in_array($filtros['estado'], ['pendiente', 'pagado', 'rechazado'], true), fn ($q) => $q->where('estado', $filtros['estado']))
            ->when($filtros['desde'] !== '', fn ($q) => $q->whereDate('fecha', '>=', $filtros['desde']))
            ->when($filtros['hasta'] !== '', fn ($q) => $q->whereDate('fecha', '<=', $filtros['hasta']))
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        $tipos = TipoCirugiaExterna::orderByDesc('precio')->get();
        $pendientes = CirugiaExterna::where('estado', 'pendiente')->count();

        // Calendario: TODAS las reservas no rechazadas (independiente del filtro/paginado).
        $reservasCalendario = CirugiaExterna::with('tipo')
            ->where('estado', '!=', 'rechazado')
            ->orderBy('fecha')->orderBy('hora_inicio')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'cirujano' => $c->cirujano_nombre,
                'paciente' => $c->paciente_nombre,
                'tipo' => $c->tipo->nombre ?? '',
                'quirofano' => 'Quirófano ' . $c->quirofano_id,
                'fecha' => $c->fecha->format('Y-m-d'),
                'hora' => substr((string) $c->hora_inicio, 0, 5),
                'hora_fin' => substr((string) $c->hora_fin, 0, 5),
                'estado' => $c->estado,
            ])->values();

        $qrUrl = CirugiaExterna::qrPagoUrl();

        return view('admin.cirugias-externas.index', compact('cirugias', 'tipos', 'pendientes', 'filtros', 'reservasCalendario', 'qrUrl'));
    }

    /**
     * Carga/reemplaza el QR de pago que se muestra en la página pública.
     * Se normaliza a PNG con fondo blanco (para que siempre escanee) y se guarda
     * en la ruta única {@see CirugiaExterna::QR_PAGO_PATH}.
     */
    public function qrUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'qr' => 'required|image|max:8192',
        ], [
            'qr.required' => 'Seleccione una imagen del QR.',
            'qr.image' => 'El QR debe ser una imagen (PNG o JPG).',
            'qr.max' => 'La imagen del QR es muy grande (máx. 8 MB).',
        ]);

        $src = @imagecreatefromstring(file_get_contents($request->file('qr')->getRealPath()));
        if ($src === false) {
            return back()->with('error', 'No se pudo procesar la imagen del QR.');
        }

        $max = 800;
        $w = imagesx($src);
        $h = imagesy($src);
        $escala = ($w > $max || $h > $max) ? min($max / $w, $max / $h) : 1.0;
        $nw = max(1, (int) round($w * $escala));
        $nh = max(1, (int) round($h * $escala));

        $canvas = imagecreatetruecolor($nw, $nh);
        imagefilledrectangle($canvas, 0, 0, $nw, $nh, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
        imagedestroy($src);
        imagedestroy($canvas);

        Storage::disk('public')->put(CirugiaExterna::QR_PAGO_PATH, $png);

        return redirect()->route('admin.cirugias-externas.index', ['tab' => 'qr'])
            ->with('success', 'QR de pago actualizado. Ya aparece en la página pública.');
    }

    public function qrDestroy(): RedirectResponse
    {
        Storage::disk('public')->delete(CirugiaExterna::QR_PAGO_PATH);

        return redirect()->route('admin.cirugias-externas.index', ['tab' => 'qr'])
            ->with('success', 'QR de pago eliminado. La página pública mostrará el marcador por defecto.');
    }

    public function show(CirugiaExterna $cirugiaExterna): View
    {
        $cirugiaExterna->load(['tipo', 'quirofano', 'verificador']);

        return view('admin.cirugias-externas.show', ['cirugia' => $cirugiaExterna]);
    }

    /**
     * Sirve la imagen del recibo leyéndola del disco local (storage/app/public),
     * SIN depender del symlink `public/storage` — así funciona igual en cualquier
     * despliegue (Windows, VM, cPanel). Protegida por el middleware de la ruta.
     */
    public function reciboImagen(CirugiaExterna $cirugiaExterna)
    {
        abort_if(
            ! $cirugiaExterna->recibo_path || ! Storage::disk('public')->exists($cirugiaExterna->recibo_path),
            404,
            'Recibo no encontrado.'
        );

        return Storage::disk('public')->response($cirugiaExterna->recibo_path)
            ->setPublic()->setMaxAge(86400);
    }

    /**
     * Miniatura optimizada del recibo (~200px, JPEG q65). Se genera UNA vez con
     * GD y se cachea en disco; las siguientes visitas la sirven directo. Ahorra
     * datos y acelera la carga de la lista (la imagen completa solo se descarga
     * al hacer "ver grande"). Gated por el middleware de la ruta.
     */
    public function reciboThumb(CirugiaExterna $cirugiaExterna)
    {
        $disk = Storage::disk('public');
        $path = $cirugiaExterna->recibo_path;
        abort_if(! $path || ! $disk->exists($path), 404, 'Recibo no encontrado.');

        $thumbPath = 'cirugias-externas/thumbs/' . pathinfo($path, PATHINFO_FILENAME) . '.jpg';

        if (! $disk->exists($thumbPath)) {
            $src = @imagecreatefromstring($disk->get($path));
            if ($src === false) {
                // No se pudo decodificar: se sirve el original como fallback.
                return $disk->response($path)->setPublic()->setMaxAge(86400);
            }

            $max = 200;
            $w = imagesx($src);
            $h = imagesy($src);
            $escala = $w >= $h ? min(1, $max / $w) : min(1, $max / $h);
            $nw = max(1, (int) round($w * $escala));
            $nh = max(1, (int) round($h * $escala));

            $thumb = imagescale($src, $nw, $nh);
            // Aplanar transparencia sobre blanco (recibos PNG/screenshots).
            $canvas = imagecreatetruecolor($nw, $nh);
            imagefilledrectangle($canvas, 0, 0, $nw, $nh, imagecolorallocate($canvas, 255, 255, 255));
            imagecopy($canvas, $thumb ?: $src, 0, 0, 0, 0, $nw, $nh);

            ob_start();
            imagejpeg($canvas, null, 65);
            $jpg = ob_get_clean();

            imagedestroy($src);
            if ($thumb) {
                imagedestroy($thumb);
            }
            imagedestroy($canvas);

            $disk->put($thumbPath, $jpg);
        }

        return $disk->response($thumbPath, null, ['Content-Type' => 'image/jpeg'])
            ->setPublic()->setMaxAge(86400);
    }

    public function verificarPago(CirugiaExterna $cirugiaExterna): RedirectResponse
    {
        if ($cirugiaExterna->estado !== 'pendiente') {
            return back()->with('error', 'Sólo se puede verificar el pago de una reserva pendiente.');
        }

        $cirugiaExterna->update([
            'estado' => 'pagado',
            'verificado_por' => auth()->id(),
            'verificado_at' => now(),
        ]);

        return back()->with('success', "Pago verificado. La cirugía {$cirugiaExterna->codigo} quedó confirmada.");
    }

    public function rechazar(Request $request, CirugiaExterna $cirugiaExterna): RedirectResponse
    {
        $data = $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        if ($cirugiaExterna->estado === 'rechazado') {
            return back()->with('error', 'La reserva ya estaba rechazada.');
        }

        $cirugiaExterna->update([
            'estado' => 'rechazado',
            'motivo_rechazo' => $data['motivo_rechazo'],
            'verificado_por' => auth()->id(),
            'verificado_at' => now(),
        ]);

        return back()->with('success', "Reserva {$cirugiaExterna->codigo} rechazada.");
    }
}
