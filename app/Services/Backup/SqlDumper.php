<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

/**
 * Volcado de la base de datos MySQL a un archivo .sql usando PHP puro (PDO),
 * sin depender del binario mysqldump ni de exec()/proc_open().
 *
 * Pensado para hosting compartido (cPanel) donde la ejecución de comandos del
 * sistema suele estar deshabilitada. El formato generado es determinista y se
 * vuelve a ejecutar con {@see SqlRunner} para restaurar.
 *
 * Memoria acotada: escribe en streaming a un handle de archivo y lee las filas
 * en lotes (LIMIT/OFFSET), nunca carga una tabla entera en RAM.
 */
class SqlDumper
{
    /** Filas por sentencia INSERT (equilibra tamaño de statement vs cantidad). */
    private const FILAS_POR_INSERT = 100;

    /** Filas leídas por lote desde la BD. */
    private const FILAS_POR_LOTE = 500;

    /**
     * Tablas excluidas del volcado: son metadata operativa del propio módulo de
     * backups. Si se incluyeran, una restauración a un punto anterior borraría el
     * historial de respaldos y la configuración. Los .zip físicos viven en disco.
     */
    private const TABLAS_EXCLUIDAS = ['backups', 'backup_settings'];

    public function __construct(private readonly string $connection = '')
    {
    }

    private function pdo(): PDO
    {
        $conn = $this->connection !== '' ? DB::connection($this->connection) : DB::connection();

        return $conn->getPdo();
    }

    /**
     * Vuelca toda la base a $rutaArchivo (ruta absoluta del sistema de archivos).
     *
     * @return int Cantidad de tablas volcadas.
     */
    public function dump(string $rutaArchivo): int
    {
        $handle = @fopen($rutaArchivo, 'w');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el archivo de volcado: {$rutaArchivo}");
        }

        try {
            $pdo = $this->pdo();
            $this->escribirCabecera($handle, $pdo);

            $tablas = $this->tablasBase($pdo);
            foreach ($tablas as $tabla) {
                $this->volcarTabla($handle, $pdo, $tabla);
            }

            $this->escribirPie($handle);

            return count($tablas);
        } finally {
            fclose($handle);
        }
    }

    private function escribirCabecera($handle, PDO $pdo): void
    {
        $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $fecha = now()->format('Y-m-d H:i:s');

        fwrite($handle, "-- Respaldo CEMSA HIS\n");
        fwrite($handle, "-- Base de datos: {$db}\n");
        fwrite($handle, "-- Generado: {$fecha}\n");
        fwrite($handle, "-- Motor: volcado PHP puro (sin mysqldump)\n\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");
    }

    private function escribirPie($handle): void
    {
        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($handle, "-- Fin del respaldo\n");
    }

    /** @return string[] Nombres de las tablas base (excluye vistas). */
    private function tablasBase(PDO $pdo): array
    {
        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tablas = [];
        while ($fila = $stmt->fetch(PDO::FETCH_NUM)) {
            if (in_array($fila[0], self::TABLAS_EXCLUIDAS, true)) {
                continue;
            }
            $tablas[] = $fila[0];
        }
        sort($tablas);

        return $tablas;
    }

    private function volcarTabla($handle, PDO $pdo, string $tabla): void
    {
        $tablaId = $this->quoteId($tabla);

        // Estructura.
        fwrite($handle, "\n-- ----------------------------\n");
        fwrite($handle, "-- Estructura de `{$tabla}`\n");
        fwrite($handle, "-- ----------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS {$tablaId};\n");

        $createRow = $pdo->query("SHOW CREATE TABLE {$tablaId}")->fetch(PDO::FETCH_NUM);
        fwrite($handle, $createRow[1].";\n\n");

        // Datos.
        $total = (int) $pdo->query("SELECT COUNT(*) FROM {$tablaId}")->fetchColumn();
        if ($total === 0) {
            return;
        }

        fwrite($handle, "-- Datos de `{$tabla}` ({$total} filas)\n");

        $offset = 0;
        while ($offset < $total) {
            $stmt = $pdo->query("SELECT * FROM {$tablaId} LIMIT ".self::FILAS_POR_LOTE." OFFSET {$offset}");
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($filas)) {
                break;
            }

            $this->escribirInserts($handle, $pdo, $tablaId, $filas);
            $offset += self::FILAS_POR_LOTE;
        }

        fwrite($handle, "\n");
    }

    /**
     * @param  array<int, array<string, mixed>>  $filas
     */
    private function escribirInserts($handle, PDO $pdo, string $tablaId, array $filas): void
    {
        $columnas = array_map(fn ($c) => $this->quoteId($c), array_keys($filas[0]));
        $columnasSql = implode(', ', $columnas);

        foreach (array_chunk($filas, self::FILAS_POR_INSERT) as $grupo) {
            $valores = [];
            foreach ($grupo as $fila) {
                $celdas = array_map(fn ($v) => $this->quoteValor($pdo, $v), array_values($fila));
                $valores[] = '('.implode(', ', $celdas).')';
            }

            fwrite(
                $handle,
                "INSERT INTO {$tablaId} ({$columnasSql}) VALUES\n".implode(",\n", $valores).";\n"
            );
        }
    }

    private function quoteId(string $identificador): string
    {
        return '`'.str_replace('`', '``', $identificador).'`';
    }

    private function quoteValor(PDO $pdo, mixed $valor): string
    {
        if ($valor === null) {
            return 'NULL';
        }

        if (is_int($valor) || is_float($valor)) {
            return (string) $valor;
        }

        // PDO::quote escapa correctamente comillas y caracteres de control.
        return $pdo->quote((string) $valor);
    }
}
