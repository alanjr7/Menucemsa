<?php

namespace App\Models;

use App\Support\TipoDocumento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    /**
     * Ruido de framework: nunca aporta a la auditoría. Se descarta tanto al
     * persistir (AuditMiddleware / ActivityLogService) como al mostrar.
     */
    public const NOISE_KEYS = ['_token', '_method', '_previous', '_', 'password_confirmation'];

    /** Campos sensibles: se conservan como evidencia pero ofuscados. */
    public const SENSITIVE_KEYS = ['password', 'remember_token', 'token', 'credit_card', 'cvv'];

    /** Metadatos de modelo sin valor en el diff (el id vive en model_id). */
    public const META_KEYS = ['id', 'created_at', 'updated_at', 'email_verified_at'];

    /** Claves cuyo valor se interpreta como booleano (Sí/No) al mostrar. */
    private const BOOLEAN_KEYS = ['requiere_receta', 'con_credito_fiscal', 'crear_nuevos', 'is_active', 'activo'];

    /** Etiquetas legibles para las claves más frecuentes de la bitácora. */
    private const KEY_LABELS = [
        'items' => 'Ítems',
        'cliente_id' => 'Cliente',
        'metodo_pago' => 'Método de pago',
        'requiere_receta' => 'Requiere receta',
        'con_credito_fiscal' => 'Crédito fiscal',
        'factura_complemento' => 'Complemento',
        'factura_razon_social' => 'Razón social',
        'factura_tipo_documento' => 'Tipo de documento',
        'factura_numero_documento' => 'Nº documento',
        'recibido_por' => 'Recibido por',
        'crear_nuevos' => 'Crear nuevos',
        'is_active' => 'Estado',
        'area' => 'Área',
        'modo' => 'Modo',
        'motivo' => 'Motivo',
        'data' => 'Detalle',
        'archivo' => 'Archivo',
    ];

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'old_values', 'new_values',
        'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    /** Valores nuevos listos para mostrar (sin ruido ni metadatos). */
    public function displayableNewValues(): array
    {
        return static::filterForDisplay($this->new_values);
    }

    /** Valores anteriores listos para mostrar (sin ruido ni metadatos). */
    public function displayableOldValues(): array
    {
        return static::filterForDisplay($this->old_values);
    }

    private static function filterForDisplay($values): array
    {
        if (!is_array($values)) {
            return [];
        }

        $hidden = array_merge(self::NOISE_KEYS, self::META_KEYS);

        return array_filter(
            $values,
            fn ($key) => !in_array($key, $hidden, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /** Etiqueta legible para una clave (mapa conocido o headline genérico). */
    public static function humanKey(string $key): string
    {
        return self::KEY_LABELS[$key] ?? Str::headline($key);
    }

    /** Normaliza un valor escalar: enums, booleanos y vacíos legibles. */
    public static function formatScalar(string $key, $value): string
    {
        if ($value === null || $value === '' || $value === []) {
            return '—';
        }

        if ($key === 'factura_tipo_documento' && is_numeric($value)) {
            return TipoDocumento::labelFor((int) $value);
        }

        if (is_bool($value) || in_array($key, self::BOOLEAN_KEYS, true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'Sí' : 'No';
        }

        return (string) $value;
    }

    /** Resumen corto de un arreglo: "N elementos" / "N campos". */
    public static function summarize(array $value): string
    {
        $n = count($value);
        if ($n === 0) {
            return '—';
        }

        $isList = array_keys($value) === range(0, $n - 1);

        return $isList
            ? $n . ' ' . ($n === 1 ? 'elemento' : 'elementos')
            : $n . ' ' . ($n === 1 ? 'campo' : 'campos');
    }

    /** JSON legible para el detalle expandible de un arreglo. */
    public static function prettyJson($value): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
