<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(?int $userId, string $action, ?string $modelType = null, ?int $modelId = null, ?array $properties = null, ?Request $request = null): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
        ]);
    }
}
