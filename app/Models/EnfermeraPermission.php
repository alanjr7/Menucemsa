<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnfermeraPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'enfermera_id',
        'permission_key',
        'granted_by',
    ];

    /**
     * Permisos disponibles en el sistema
     */
    public const AVAILABLE_PERMISSIONS = [
        'ver_pacientes' => [
            'label' => 'Ver Pacientes',
            'description' => 'Ver lista de pacientes en emergencia',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'default' => true,
        ],
        'registrar_signos_vitales' => [
            'label' => 'Registrar Signos Vitales',
            'description' => 'Registrar presión arterial, frecuencia cardíaca, temperatura, etc.',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'default' => true,
        ],
        'cambiar_estados' => [
            'label' => 'Cambiar Estados',
            'description' => 'Cambiar estados del paciente (recibido → en evaluación → estabilizado)',
            'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'default' => true,
        ],
        'aplicar_medicamentos' => [
            'label' => 'Aplicar Medicamentos',
            'description' => 'Aplicar medicamentos desde el inventario de emergencia',
            'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'default' => false,
        ],
        'ver_historial' => [
            'label' => 'Ver Historial',
            'description' => 'Acceder al historial médico del paciente',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'default' => true,
        ],
        'derivar_pacientes' => [
            'label' => 'Derivar Pacientes',
            'description' => 'Enviar pacientes a cirugía, UTI u hospitalización',
            'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
            'default' => false,
        ],
        'dar_alta' => [
            'label' => 'Dar de Alta',
            'description' => 'Dar de alta a pacientes (solo enfermeras senior)',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'default' => false,
        ],
    ];

    /**
     * Get permission label
     */
    public static function getPermissionLabel(string $key): string
    {
        return self::AVAILABLE_PERMISSIONS[$key]['label'] ?? $key;
    }

    /**
     * Get permission description
     */
    public static function getPermissionDescription(string $key): string
    {
        return self::AVAILABLE_PERMISSIONS[$key]['description'] ?? '';
    }

    /**
     * Get default permissions for new nurses
     */
    public static function getDefaultPermissions(): array
    {
        return collect(self::AVAILABLE_PERMISSIONS)
            ->filter(fn($perm) => $perm['default'])
            ->keys()
            ->toArray();
    }

    /**
     * Relationship to Enfermera
     */
    public function enfermera(): BelongsTo
    {
        return $this->belongsTo(Enfermera::class, 'enfermera_id', 'user_id');
    }

    /**
     * Relationship to User who granted the permission
     */
    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
