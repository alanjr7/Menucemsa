<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public static $patientContext = null;

    protected $fillable = [
        'id',
        'fecha',
        'total_dia',
        'tipo',
        'nro_factura',
        'id_farmacia',
        'metodo_pago',
        'referencia',
        'estado',
    ];

    protected $casts = [
        'id' => 'string',
        'fecha' => 'datetime',
        'total_dia' => 'float',
        'nro_factura' => 'integer',
    ];

    /**
     * @deprecated Tabla 'facturas' no existe. Usar CuentaCobro para cobros.
     */
    public function factura()
    {
        return $this;
    }

    /**
     * @deprecated Usar VentaFarmacia con farmacia_id.
     */
    public function farmacia()
    {
        return $this;
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'caja_id', 'id');
    }

    public function consulta()
    {
        return $this->hasOne(Consulta::class, 'caja_id', 'id');
    }

    // Métodos para simular estado (ya que no existe en la BD)
    public function getEstadoAttribute()
    {
        // Si tiene factura, está pagado, si no, pendiente
        return $this->nro_factura ? 'pagado' : 'pendiente';
    }

    public function getMontoPagadoAttribute()
    {
        return $this->total_dia;
    }

    public function marcarComoPagado()
    {
        // Asignar número de factura para marcar como pagado
        $this->nro_factura = $this->generarNumeroFactura();
        $this->save();
    }

    public function getEstadoColorAttribute()
    {
        return $this->estado === 'pagado' ? 'green' : 'yellow';
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($caja) {
            if (empty($caja->id)) {
                if (self::$patientContext) {
                    $caja->id = self::generarCodigoPaciente(self::$patientContext);
                } else {
                    $caja->id = 'CAJA-' . date('YmdHis') . '-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
                }
            }
            if (empty($caja->fecha)) {
                $caja->fecha = now();
            }
        });
    }

    public static function generarCodigoPaciente($paciente)
    {
        $fechaNac = \Carbon\Carbon::parse($paciente->fecha_nacimiento);
        $yy = $fechaNac->format('y');

        $mes = (int) $fechaNac->format('m');
        if ($paciente->sexo === 'F') {
            $mes += 50;
        }
        $mm = str_pad($mes, 2, '0', STR_PAD_LEFT);

        $dd = $fechaNac->format('d');

        $nombreCompleto = strtoupper(trim($paciente->nombre));
        $partes = explode(' ', $nombreCompleto);

        $inicialNombre = substr(end($partes), 0, 1);

        if (count($partes) >= 3) {
            $inicialPaterno = substr($partes[count($partes) - 2], 0, 1);
            $inicialMaterno = substr($partes[count($partes) - 3], 0, 1);
        } elseif (count($partes) === 2) {
            $inicialPaterno = substr($partes[0], 0, 1);
            $inicialMaterno = 'X';
        } else {
            $inicialPaterno = substr($partes[0], 0, 1);
            $inicialMaterno = 'X';
            if (strlen($nombreCompleto) > 1) {
                $inicialMaterno = substr($nombreCompleto, 1, 1);
            }
        }

        $codigoBase = 'REG-' . $yy . '-' . $mm . $dd . '-' . $inicialPaterno . $inicialMaterno . $inicialNombre;

        $codigo = $codigoBase;
        $i = 1;

        while (self::where('id', $codigo)->exists()) {
            $codigo = $codigoBase . '-' . $i;
            $i++;
        }

        return $codigo;
    }

    private function generarNumeroFactura()
    {
        // Obtener el último número de factura y generar el siguiente
        $ultimaFactura = Caja::whereNotNull('nro_factura')->max('nro_factura') ?? 0;
        return $ultimaFactura + 1;
    }
}
