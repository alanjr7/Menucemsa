<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public static function log($action, $description, $model = null, $oldValues = null, $newValues = null)
    {
        $user = auth()->user();
        
        if (!$user) return;

        // Limpiar los valores para mostrar solo los campos relevantes
        $cleanOldValues = self::cleanValues($oldValues);
        $cleanNewValues = self::cleanValues($newValues);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $cleanOldValues,
            'new_values' => $cleanNewValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private static function cleanValues($values)
    {
        if (!$values) return null;

        // Si es un array asociativo, filtrar campos sensibles
        if (is_array($values)) {
            $sensitiveFields = ['password', 'remember_token'];
            
            // Si solo tiene un campo (como is_active), mostrarlo directamente
            if (count($values) === 1) {
                return $values;
            }
            
            // Si tiene múltiples campos, filtrar los sensibles
            $cleaned = [];
            foreach ($values as $key => $value) {
                if (!in_array($key, $sensitiveFields)) {
                    $cleaned[$key] = $value;
                }
            }
            
            return $cleaned;
        }

        return $values;
    }
}
