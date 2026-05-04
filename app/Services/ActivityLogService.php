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
            'model_id' => $model ? $model->getKey() : null,
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

    public static function logCreate($model, $description = null, $values = null)
    {
        $defaultDesc = self::getDefaultDescription('creo', $model);
        self::log('create', $description ? $description : $defaultDesc, $model, null, $values);
    }

    public static function logUpdate($model, $description = null, $oldValues = null, $newValues = null)
    {
        if (empty($newValues)) {
            return;
        }
        $defaultDesc = self::getDefaultDescription('actualizo', $model);
        self::log('update', $description ? $description : $defaultDesc, $model, $oldValues, $newValues);
    }

    public static function logDelete($model, $description = null)
    {
        $defaultDesc = self::getDefaultDescription('elimino', $model);
        self::log('delete', $description ? $description : $defaultDesc, $model);
    }

    public static function logAction($action, $module, $description, $model = null, $values = null)
    {
        $fullAction = $action . '_' . $module;
        self::log($fullAction, $description, $model, null, $values);
    }

    public static function logModuleAction($module, $action, $entity, $entityName, $details = null)
    {
        $description = ucfirst($action) . ' ' . $entity . ': ' . $entityName;
        if ($details) {
            $description .= ' - ' . $details;
        }
        self::logAction($action, $module, $description, null, $details);
    }

    private static function getDefaultDescription($action, $model)
    {
        $className = class_basename($model);
        $modelName = $model->name ? $model->name : ($model->nombre ? $model->nombre : ($model->code ? $model->code : ($model->id ? $model->id : 'ID ' . $model->getKey())));
        return ucfirst($action) . ' ' . $className . ': ' . $modelName;
    }
}
