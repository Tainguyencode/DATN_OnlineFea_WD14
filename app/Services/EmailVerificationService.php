<?php

namespace App\Services;

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\VerifyEmailCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class EmailVerificationService
{
    public function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);

        if (strlen($local) <= 4) {
            $maskedLocal = substr($local, 0, 1).str_repeat('*', max(1, strlen($local) - 1));
        } else {
            $maskedLocal = substr($local, 0, 2).str_repeat('*', strlen($local) - 4).substr($local, -2);
        }

        return $maskedLocal.'@'.$domain;
    }

    public function sendCode(User $user, bool $ignoreCooldown = false): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $latest = $this->latestCode($user);

        if (! $ignoreCooldown && $latest?->last_sent_at !== null) {
            $secondsRemaining = EmailVerificationCode::RESEND_COOLDOWN_SECONDS - $latest->last_sent_at->diffInSeconds(now());

            if ($secondsRemaining > 0) {
                throw ValidationException::withMessages([
                    'email' => 'Vui lòng đợi '.(int) ceil($secondsRemaining).' giây trước khi gửi lại mã.',
                ]);
            }
        }

        $this->invalidateActiveCodes($user);

        $plainCode = $this->generateCode();

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(EmailVerificationCode::EXPIRY_MINUTES),
            'last_sent_at' => now(),
        ]);

        $user->notify(new VerifyEmailCodeNotification($plainCode));
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function verify(User $user, string $code): array
    {
        if ($user->hasVerifiedEmail()) {
            return [
                'success' => true,
                'message' => 'Email đã được xác thực.',
            ];
        }

        $record = $this->latestCode($user);

        if (! $record || $record->used_at !== null) {
            return [
                'success' => false,
                'message' => 'Mã xác thực không chính xác.',
            ];
        }

        if ($record->expires_at->isPast()) {
            $record->forceFill(['used_at' => now()])->save();

            return [
                'success' => false,
                'message' => 'Mã xác thực đã hết hạn. Vui lòng gửi lại mã mới.',
            ];
        }

        if ($record->attempt_count >= EmailVerificationCode::MAX_ATTEMPTS) {
            $record->forceFill(['used_at' => now()])->save();

            return [
                'success' => false,
                'message' => 'Mã xác thực đã bị vô hiệu do nhập sai quá nhiều lần. Vui lòng gửi lại mã mới.',
            ];
        }

        if (! Hash::check($code, $record->code_hash)) {
            $record->increment('attempt_count');

            if ($record->fresh()->attempt_count >= EmailVerificationCode::MAX_ATTEMPTS) {
                $record->forceFill(['used_at' => now()])->save();

                return [
                    'success' => false,
                    'message' => 'Mã xác thực đã bị vô hiệu do nhập sai quá nhiều lần. Vui lòng gửi lại mã mới.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Mã xác thực không chính xác.',
            ];
        }

        $record->forceFill(['used_at' => now()])->save();
        $this->invalidateActiveCodes($user, exceptId: $record->id);

        if (! $user->markEmailAsVerified()) {
            throw new RuntimeException('Unable to mark email as verified.');
        }

        return [
            'success' => true,
            'message' => 'Email đã được xác thực thành công.',
        ];
    }

    public function resendCooldownSeconds(User $user): int
    {
        $latest = $this->latestCode($user);

        if ($latest?->last_sent_at === null) {
            return 0;
        }

        $remaining = EmailVerificationCode::RESEND_COOLDOWN_SECONDS - $latest->last_sent_at->diffInSeconds(now());

        return max(0, $remaining);
    }

    public function invalidateActiveCodes(User $user, ?int $exceptId = null): void
    {
        EmailVerificationCode::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->update(['used_at' => now()]);
    }

    public function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function latestCode(User $user): ?EmailVerificationCode
    {
        return EmailVerificationCode::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();
    }
}
