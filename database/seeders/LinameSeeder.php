<?php

namespace Database\Seeders;

use App\Models\AlmacenCatalogo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Precarga el catálogo del almacén central con la LINAME
 * (Lista Nacional de Medicamentos Esenciales, Bolivia).
 *
 * Crea SOLO catálogo (presentaciones), sin lotes ni stock: cada ítem queda en 0.
 * Cuando la clínica reciba un medicamento real, se le agrega un lote+stock al
 * catálogo que ya existe, en vez de darlo de alta uno por uno.
 *
 * Idempotente: se identifica cada presentación por su nombre normalizado
 * (principio activo + concentración + forma) y se usa updateOrCreate, por lo
 * que correrlo de nuevo actualiza en lugar de duplicar.
 *
 * Fuente: database/data/liname.txt (TSV de 6 columnas).
 */
class LinameSeeder extends Seeder
{
    /** Mapa letra ATC (1er nivel anatómico) → categoría legible. */
    private const GRUPOS_ATC = [
        'A' => 'Tracto digestivo y metabolismo',
        'B' => 'Sangre y órganos hematopoyéticos',
        'C' => 'Sistema cardiovascular',
        'D' => 'Dermatológicos',
        'G' => 'Sistema genitourinario y hormonas sexuales',
        'H' => 'Hormonas sistémicas (excl. sexuales)',
        'J' => 'Antiinfecciosos vía general',
        'L' => 'Antineoplásicos e inmunomoduladores',
        'M' => 'Sistema musculoesquelético',
        'N' => 'Sistema nervioso',
        'P' => 'Antiparasitarios, insecticidas y repelentes',
        'R' => 'Sistema respiratorio',
        'S' => 'Órganos de los sentidos',
        'V' => 'Varios',
    ];

    public function run(): void
    {
        $ruta = database_path('data/liname.txt');

        if (! is_file($ruta)) {
            $this->command?->error("No se encontró el archivo de datos LINAME: {$ruta}");

            return;
        }

        $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_shift($lineas); // descartar encabezado

        $creados = 0;
        $actualizados = 0;
        $omitidos = 0;
        $vistos = [];

        DB::transaction(function () use ($lineas, &$creados, &$actualizados, &$omitidos, &$vistos) {
            foreach ($lineas as $linea) {
                $partes = explode("\t", rtrim($linea, "\t\r\n"));

                // Anclado al final (la columna ATC siempre es la última fiable):
                // [codigo, ...medicamento, concentracion, forma, m_res, atc]
                if (count($partes) < 6) {
                    $omitidos++;

                    continue;
                }

                $atc          = trim(array_pop($partes));
                $mRes         = trim(array_pop($partes));
                $forma        = trim(array_pop($partes));
                $concentracion = trim(array_pop($partes));
                $codigoLiname = trim((string) ($partes[0] ?? ''));
                $medicamento  = trim(implode(' ', array_slice($partes, 1)));

                if ($medicamento === '') {
                    $omitidos++;

                    continue;
                }

                // Nombre = presentación completa (cómo lo lee un farmacéutico).
                $nombre = $this->limpiarEspacios("{$medicamento} {$concentracion} {$forma}");
                $clave  = mb_strtolower($nombre);

                // Evitar reprocesar una presentación idéntica dentro del mismo archivo.
                if (isset($vistos[$clave])) {
                    $omitidos++;

                    continue;
                }
                $vistos[$clave] = true;

                $existe = AlmacenCatalogo::whereRaw('LOWER(nombre) = ?', [$clave])->exists();

                AlmacenCatalogo::updateOrCreate(
                    ['nombre' => $nombre],
                    [
                        'nombre_generico'    => $medicamento,
                        'concentracion'      => $concentracion ?: null,
                        'forma_farmaceutica' => $forma ?: null,
                        'categoria'          => $this->categoriaDesdeAtc($atc),
                        'codigo_atc'         => $atc ?: null,
                        'codigo_liname'      => $codigoLiname ?: null,
                        'unidad_medida'      => $this->unidadDesdeForma($forma),
                        'tipo'               => 'medicamento',
                        'requiere_receta'    => $mRes !== '',
                        'activo'             => true,
                        'observaciones'      => $mRes !== '' ? "LINAME M.Res.: {$mRes}" : null,
                    ]
                );

                $existe ? $actualizados++ : $creados++;
            }
        });

        $this->command?->info(
            "LINAME: {$creados} creados, {$actualizados} actualizados, {$omitidos} omitidos."
        );
    }

    /** Deriva la unidad de inventario por defecto a partir de la forma farmacéutica. */
    private function unidadDesdeForma(string $forma): string
    {
        $f = mb_strtolower($forma);

        $liquidos = ['jarabe', 'solución oral', 'solucion oral', 'suspensión', 'suspension',
            'emulsión', 'emulsion', 'gotas', 'solución nasal', 'solucion nasal',
            'solución oftálmica', 'solucion oftalmica', 'gotas óticas', 'gotas oticas', 'loción', 'locion'];

        foreach ($liquidos as $aguja) {
            if (str_contains($f, $aguja)) {
                return 'frascos';
            }
        }

        if (str_contains($f, 'sobres')) {
            return 'sobres';
        }

        return 'unidades';
    }

    /** Categoría legible a partir de la 1ª letra del código ATC. */
    private function categoriaDesdeAtc(string $atc): ?string
    {
        $letra = mb_strtoupper(mb_substr($atc, 0, 1));

        return self::GRUPOS_ATC[$letra] ?? null;
    }

    private function limpiarEspacios(string $texto): string
    {
        return trim(preg_replace('/\s+/', ' ', $texto));
    }
}
