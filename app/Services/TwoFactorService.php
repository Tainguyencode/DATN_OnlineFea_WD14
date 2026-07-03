<?php

namespace App\Services;

use App\Models\TwoFactorCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TwoFactorService
{
    public function sendCode(User $user): string
    {
        $code = (string) random_int(100000, 999999);

        TwoFactorCode::where('user_id', $user->id)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Mã xác thực 2FA Website học online FEA của bạn là: {$code}. Mã có hiệu lực trong 10 phút.", function ($message) use ($user): void {
            $message->to($user->email)->subject('Mã xác thực 2FA Website học online FEA');
        });

        return $code;
    }

    public function verify(User $user, string $code): bool
    {
        $record = TwoFactorCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            return false;
        }

        $record->update(['is_used' => true]);

        return true;
    }
}
