<?php

namespace App\Traits;

use App\Jobs\LogAuditJob;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit('Created', $model);
        });

        static::updated(function ($model) {
            self::logAudit('Updated', $model, $model->getChanges());
        });

        static::deleted(function ($model) {
            self::logAudit('Deleted', $model);
        });
        
        // Handle restoration if SoftDeletes is used
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                self::logAudit('Restored', $model);
            });
        }
    }

    protected static function logAudit($action, $model, $changes = null)
    {
        $user = auth()->user();
        $userId = $user ? $user->id : null;
        
        // Provide fallback strings if auth user is not available (e.g. system jobs)
        // or getting request info fails in CLI environment
        $ip = request()->ip() ?? '127.0.0.1'; 
        $userAgent = request()->userAgent() ?? 'System/CLI';

        $className = class_basename($model);
        
        $meta = [
            'model' => get_class($model),
            'model_id' => $model->id,
            'details' => $model->toArray()
        ];

        // For updates, try to show what changed
        if ($action === 'Updated' && $changes) {
            // Get original values for changed attributes
            $original = [];
            foreach ($changes as $key => $value) {
                // Skip timestamps unless specifically needed, usually checking 'updated_at' is noise
                if ($key === 'updated_at') continue;
                
                $original[$key] = $model->getOriginal($key);
            }
            
            // If only updated_at changed, we might skip or log it anyway. 
            // Let's log if there are meaningful changes or if $changes has other keys
            if (!empty($original)) {
                $meta['changes'] = [
                    'before' => $original,
                    'after' => array_intersect_key($changes, $original)
                ];
            }
        }
        
        // Construct a readable event string, e.g. "Created Project", "Updated Karyawan"
        $event = "{$action} {$className}";

        LogAuditJob::dispatch($userId, $event, $ip, $userAgent, ($meta));
    }
}
