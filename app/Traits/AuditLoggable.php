<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait AuditLoggable
{
    protected function logActivity(string $action, string $description, $model = null): void
    {
        ActivityLogService::log($action, $description, $model);
    }

    protected function logWithChanges(
        string $action,
        string $description,
        $model,
        array $oldValues,
        array $newValues
    ): void {
        ActivityLogService::log($action, $description, $model, $oldValues, $newValues);
    }

    protected function logCreate($model, string $description = null): void
    {
        $defaultDescription = $this->getDefaultDescription('crear', $model);
        ActivityLogService::log('create', $description ? $description : $defaultDescription, $model);
    }

    protected function logUpdate($model, array $changes, string $description = null): void
    {
        if (empty($changes)) {
            return;
        }

        $defaultDescription = $this->getDefaultDescription('actualizar', $model);
        ActivityLogService::log(
            'update',
            $description ? $description : $defaultDescription,
            $model,
            $model->getOriginal(),
            $changes
        );
    }

    protected function logDelete($model, string $description = null): void
    {
        $defaultDescription = $this->getDefaultDescription('eliminar', $model);
        ActivityLogService::log('delete', $description ? $description : $defaultDescription, $model);
    }

    protected function logIf(bool $condition, string $action, string $description, $model = null): void
    {
        if ($condition) {
            ActivityLogService::log($action, $description, $model);
        }
    }

    private function getDefaultDescription(string $action, $model): string
    {
        $className = class_basename($model);
        $modelName = $model->name ? $model->name : ($model->nombre ? $model->nombre : ($model->code ? $model->code : ($model->id ? $model->id : 'ID ' . $model->getKey())));

        $actions = [
            'crear' => 'creado',
            'actualizar' => 'actualizado',
            'eliminar' => 'eliminado',
        ];

        $actionLabel = isset($actions[$action]) ? $actions[$action] : $action;
        return "{$className} {$actionLabel}: {$modelName}";
    }
}
