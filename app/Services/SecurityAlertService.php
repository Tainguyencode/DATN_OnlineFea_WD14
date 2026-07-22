<?php

namespace App\Services;

use App\Models\SecurityAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class SecurityAlertService
{
    /**
     * Ghi nhận cảnh báo bảo mật.
     * Nếu bảng security_alerts chưa tồn tại thì chỉ ghi Log, không crash ứng dụng.
     *
     * @param string $type NEW_DEVICE | MULTIPLE_LOGIN | MULTIPLE_IP | TOKEN_INVALID | SESSION_KICKED
     */
    public function logAlert(string $type, ?int $userId, array $details = []): void
    {
        try {
            if (! Schema::hasTable('security_alerts')) {
                Log::warning("[SecurityAlert] Bảng security_alerts chưa tồn tại. Type={$type} user_id={$userId}");
                return;
            }

            SecurityAlert::create([
                'user_id'    => $userId,
                'type'       => $type,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'details'    => $details,
            ]);
        } catch (\Throwable $e) {
            Log::error("[SecurityAlert] Không thể ghi cảnh báo: " . $e->getMessage(), [
                'type'    => $type,
                'user_id' => $userId,
            ]);
        }
    }
}
