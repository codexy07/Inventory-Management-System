<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Log a "created" activity.
     */
    public static function created(Model $model, ?string $description = null): ActivityLog
    {
        return self::log('created', $model, $description ?? self::defaultDescription('created', $model));
    }

    /**
     * Log an "updated" activity.
     */
    public static function updated(Model $model, array $original = [], ?string $description = null): ActivityLog
    {
        $changes = [];
        foreach ($original as $field => $oldValue) {
            $newValue = $model->{$field};
            if ((string) $oldValue !== (string) $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return self::log('updated', $model, $description ?? self::defaultDescription('updated', $model), $changes);
    }

    /**
     * Log a "deleted" activity.
     */
    public static function deleted(Model $model, ?string $description = null): ActivityLog
    {
        return self::log('deleted', $model, $description ?? self::defaultDescription('deleted', $model));
    }

    /**
     * Core logging method.
     */
    protected static function log(string $action, Model $model, string $description, array $changes = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => class_basename(get_class($model)),
            'model_id'   => $model->getKey(),
            'description' => $description,
            'changes'    => !empty($changes) ? $changes : null,
        ]);
    }

    /**
     * Generate a default human-readable description.
     */
    protected static function defaultDescription(string $action, Model $model): string
    {
        $modelName = class_basename(get_class($model));
        $identifier = method_exists($model, 'getNameAttribute')
            ? $model->name
            : ($model->name ?? $model->getKey());

        return match ($action) {
            'created' => "Created {$modelName}: \"{$identifier}\"",
            'updated' => "Updated {$modelName}: \"{$identifier}\"",
            'deleted' => "Deleted {$modelName}: \"{$identifier}\"",
            default  => "{$action} {$modelName}: \"{$identifier}\"",
        };
    }
}
