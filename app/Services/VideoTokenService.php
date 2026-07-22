<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VideoTokenService
{
    private const TOKEN_PREFIX = 'video_token_';
    private const TOKEN_LIFETIME = 600; // 10 phút

    /**
     * Tạo token mới cho bài học
     */
    public function generateToken(int $userId, int $lessonId): string
    {
        $token = Str::random(60);
        $cacheKey = self::TOKEN_PREFIX . $token;

        $data = [
            'user_id' => $userId,
            'lesson_id' => $lessonId,
        ];

        Cache::put($cacheKey, $data, self::TOKEN_LIFETIME);

        return $token;
    }

    /**
     * Xác thực token
     */
    public function verifyToken(string $token, int $lessonId): bool
    {
        $cacheKey = self::TOKEN_PREFIX . $token;
        
        $data = Cache::get($cacheKey);

        if (!$data) {
            return false;
        }

        return $data['lesson_id'] === $lessonId;
    }

    /**
     * Lấy user_id từ token
     */
    public function getUserIdFromToken(string $token): ?int
    {
        $cacheKey = self::TOKEN_PREFIX . $token;
        $data = Cache::get($cacheKey);
        
        return $data['user_id'] ?? null;
    }
}
