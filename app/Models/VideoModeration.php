<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoModeration extends Model
{
    protected $fillable = [
        'lesson_id',
        'violence',
        'adult',
        'weapon',
        'tiktok_logo',
        'youtube_logo',
        'watermark',
        'copyright_risk',
        'summary',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'violence' => 'boolean',
            'adult' => 'boolean',
            'weapon' => 'boolean',
            'tiktok_logo' => 'boolean',
            'youtube_logo' => 'boolean',
            'watermark' => 'boolean',
            'details' => 'array',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function hasViolations(): bool
    {
        if ($this->violence || $this->adult || $this->weapon) {
            return true;
        }

        if ($this->watermark || $this->tiktok_logo || $this->youtube_logo) {
            return true;
        }

        return in_array(strtolower((string) $this->copyright_risk), ['medium', 'high'], true);
    }

    /**
     * @return list<array{label: string, tone: string, emoji: string}>
     */
    public function summaryBadgeItems(): array
    {
        $items = [];

        if ($this->violence) {
            $items[] = ['label' => 'Bạo lực', 'tone' => 'red', 'emoji' => '🔴'];
        }

        if ($this->adult) {
            $items[] = ['label' => 'Nội dung 18+', 'tone' => 'red', 'emoji' => '🔴'];
        }

        if ($this->weapon) {
            $items[] = ['label' => 'Vũ khí', 'tone' => 'orange', 'emoji' => '🟠'];
        }

        if ($this->watermark) {
            $items[] = ['label' => 'Watermark', 'tone' => 'orange', 'emoji' => '🟠'];
        }

        if ($this->tiktok_logo) {
            $items[] = ['label' => 'Logo TikTok', 'tone' => 'orange', 'emoji' => '🟠'];
        }

        if ($this->youtube_logo) {
            $items[] = ['label' => 'Logo YouTube', 'tone' => 'orange', 'emoji' => '🟠'];
        }

        $risk = strtolower((string) ($this->copyright_risk ?? 'low'));

        if ($risk === 'high') {
            $items[] = ['label' => 'Nguy cơ bản quyền Cao', 'tone' => 'red', 'emoji' => '🔴'];
        } elseif ($risk === 'medium') {
            $items[] = ['label' => 'Nguy cơ bản quyền Trung bình', 'tone' => 'yellow', 'emoji' => '🟡'];
        } elseif ($risk === 'low' && $this->hasViolations()) {
            $items[] = ['label' => 'Nguy cơ bản quyền Thấp', 'tone' => 'green', 'emoji' => '🟢'];
        }

        return $items;
    }

    /**
     * @return list<array{timestamp: string, labels: list<string>, reason: string}>
     */
    public function violatedFrameDetails(): array
    {
        if (! is_array($this->details) || $this->details === []) {
            return [];
        }

        $frames = [];

        foreach ($this->details as $detail) {
            if (! is_array($detail)) {
                continue;
            }

            $labels = self::frameViolationLabels($detail);

            if ($labels === []) {
                continue;
            }

            $frames[] = [
                'timestamp' => self::formatTimestamp($detail['timestamp'] ?? 0),
                'labels' => $labels,
                'reason' => trim((string) ($detail['reason'] ?? '')),
            ];
        }

        return $frames;
    }

    public static function formatTimestamp(int|float|string|null $seconds): string
    {
        $seconds = max(0, (int) $seconds);
        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $remaining);
    }

    /**
     * @return list<string>
     */
    private static function frameViolationLabels(array $detail): array
    {
        $labels = [];

        if (! empty($detail['violence'])) {
            $labels[] = 'Bạo lực';
        }

        if (! empty($detail['adult'])) {
            $labels[] = 'Nội dung 18+';
        }

        if (! empty($detail['weapon'])) {
            $labels[] = 'Vũ khí';
        }

        if (! empty($detail['watermark'])) {
            $labels[] = 'Watermark';
        }

        if (! empty($detail['tiktok_logo'])) {
            $labels[] = 'Logo TikTok';
        }

        if (! empty($detail['youtube_logo'])) {
            $labels[] = 'Logo YouTube';
        }

        $risk = strtolower((string) ($detail['copyright_risk'] ?? 'low'));

        if ($risk === 'high') {
            $labels[] = 'Nguy cơ bản quyền cao';
        } elseif ($risk === 'medium') {
            $labels[] = 'Nguy cơ bản quyền trung bình';
        }

        return $labels;
    }
}
