<?php

namespace App\Services\Backup;

use PDO;
use RuntimeException;

/**
 * Ejecuta un archivo .sql (generado por {@see SqlDumper}) sentencia por sentencia.
 *
 * Lee el archivo en streaming y separa las sentencias respetando cadenas entre
 * comillas (', ", `) y escapes de barra invertida, de modo que un ';' dentro de
 * un valor no rompa el corte. No carga el archivo completo en memoria.
 */
class SqlRunner
{
    /**
     * Ejecuta todas las sentencias del archivo contra la conexión PDO dada.
     *
     * @return int Cantidad de sentencias ejecutadas.
     */
    public function ejecutarArchivo(string $rutaArchivo, PDO $pdo): int
    {
        $handle = @fopen($rutaArchivo, 'r');
        if ($handle === false) {
            throw new RuntimeException("No se pudo leer el volcado SQL: {$rutaArchivo}");
        }

        // Blindaje extra: las FK quedan desactivadas durante toda la restauración
        // por si el volcado no lo trae o falla a mitad de camino.
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        $ejecutadas = 0;
        $inString = null;     // comilla de apertura actual o null
        $prevBackslash = false;
        $buffer = '';

        try {
            while (! feof($handle)) {
                $chunk = fread($handle, 16384);
                if ($chunk === false) {
                    break;
                }

                $len = strlen($chunk);
                for ($i = 0; $i < $len; $i++) {
                    $c = $chunk[$i];

                    if ($inString !== null) {
                        $buffer .= $c;
                        if ($prevBackslash) {
                            $prevBackslash = false;
                            continue;
                        }
                        if ($c === '\\') {
                            $prevBackslash = true;
                            continue;
                        }
                        if ($c === $inString) {
                            $inString = null;
                        }
                        continue;
                    }

                    if ($c === "'" || $c === '"' || $c === '`') {
                        $inString = $c;
                        $buffer .= $c;
                        continue;
                    }

                    if ($c === ';') {
                        if ($this->ejecutar($pdo, $buffer)) {
                            $ejecutadas++;
                        }
                        $buffer = '';
                        continue;
                    }

                    $buffer .= $c;
                }
            }

            // Última sentencia sin ';' final (por las dudas).
            if ($this->ejecutar($pdo, $buffer)) {
                $ejecutadas++;
            }
        } finally {
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            fclose($handle);
        }

        return $ejecutadas;
    }

    /**
     * Limpia comentarios de línea, y ejecuta solo si queda SQL real.
     *
     * @return bool true si ejecutó algo.
     */
    private function ejecutar(PDO $pdo, string $sentencia): bool
    {
        $sql = $this->limpiar($sentencia);
        if ($sql === '') {
            return false;
        }

        $pdo->exec($sql);

        return true;
    }

    /** Quita líneas de comentario (-- ...) y espacios; deja el SQL ejecutable. */
    private function limpiar(string $sentencia): string
    {
        $lineas = preg_split('/\r\n|\r|\n/', $sentencia) ?: [];
        $utiles = [];
        foreach ($lineas as $linea) {
            $trim = ltrim($linea);
            if ($trim === '' || str_starts_with($trim, '--')) {
                continue;
            }
            $utiles[] = $linea;
        }

        return trim(implode("\n", $utiles));
    }
}
