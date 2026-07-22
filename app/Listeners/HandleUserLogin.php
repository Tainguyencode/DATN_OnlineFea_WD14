<?php

namespace App\Listeners;

use App\Models\ActiveSession;
use App\Services\SecurityAlertService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class HandleUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct(private SecurityAlertService $alertService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionId = session()->getId();
        
        $ipAddress = Request::ip();
        $userAgent = Request::userAgent();
        $deviceId = $this->generateDeviceId($ipAddress, $userAgent);

        // Check for multiple login
        $activeSessionsCount = ActiveSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('session_id', '!=', $sessionId)
            ->count();
            
        if ($activeSessionsCount > 0) {
            $this->alertService->logAlert('MULTIPLE_LOGIN', $user->id, [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
            
            // Deactivate old sessions
            ActiveSession::where('user_id', $user->id)
                ->where('session_id', '!=', $sessionId)
                ->update(['is_active' => false]);
        }

        // Check for new device
        $hasUsedDeviceBefore = ActiveSession::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->exists();
            
        if (!$hasUsedDeviceBefore && ActiveSession::where('user_id', $user->id)->exists()) {
            $this->alertService->logAlert('NEW_DEVICE', $user->id, [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
        }

        // Create or update current session
        ActiveSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $user->id,
                'device_id' => $deviceId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'browser' => $this->getBrowserName($userAgent),
                'platform' => $this->getPlatformName($userAgent),
                'device_name' => $this->getDeviceType($userAgent),
                'is_active' => true,
                'last_activity' => now(),
            ]
        );
    }
    
    private function generateDeviceId($ip, $ua)
    {
        return md5($ip . $ua);
    }
    
    private function getBrowserName($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    private function getPlatformName($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) return 'iOS';
        return 'Unknown';
    }

    private function getDeviceType($userAgent)
    {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            return 'Tablet';
        }
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            return 'Mobile';
        }
        return 'Desktop';
    }
}
