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
     * Trả về true nếu có bất kỳ dấu hiệu nào AI phát hiện (bao gồm cả logo/watermark ở mức low).
     */
    public function hasDetectedSigns(): bool
    {
        if ($this->violence || $this->adult || $this->weapon) {
            return true;
        }

        if ($this->watermark || $this->tiktok_logo || $this->youtube_logo) {
            return true;
        }

        $risk = strtolower((string) ($this->copyright_risk ?? 'none'));
        return in_array($risk, ['low', 'medium', 'high'], true);
    }

    /**
     * Badge mức risk bản quyền theo kiểu detection-only (Udemy/Coursera style).
     */
    public function copyrightRiskBadge(): array
    {
        $risk = strtolower((string) ($this->copyright_risk ?? 'none'));

        return match ($risk) {
            'high'   => [
                'label' => 'AI đánh giá mức nghi ngờ cao – cần admin xác minh',
                'tone'  => 'red',
                'emoji' => '🔴',
            ],
            'medium' => [
                'label' => 'Nên xem lại video',
                'tone'  => 'orange',
                'emoji' => '🟠',
            ],
            'low'    => [
                'label' => 'Có dấu hiệu cần kiểm tra',
                'tone'  => 'yellow',
                'emoji' => '🟡',
            ],
            default  => [
                'label' => 'Không phát hiện dấu hiệu',
                'tone'  => 'green',
                'emoji' => '🟢',
            ],
        };
    }

    /**
     * @return list<array{label: string, tone: string, emoji: string}>
     */
    public function summaryBadgeItems(): array
    {
        $items = [];

        if ($this->violence) {
            $items[] = ['label' => 'Phát hiện: Bạo lực', 'tone' => 'red', 'emoji' => '🔴'];
        }

        if ($this->adult) {
            $items[] = ['label' => 'Phát hiện: Nội dung 18+', 'tone' => 'red', 'emoji' => '🔴'];
        }

        if ($this->weapon) {
            $items[] = ['label' => 'Phát hiện: Vũ khí', 'tone' => 'orange', 'emoji' => '🟠'];
        }

        if ($this->watermark) {
            $items[] = ['label' => 'Dấu hiệu: Watermark', 'tone' => 'yellow', 'emoji' => '🟡'];
        }

        if ($this->tiktok_logo) {
            $items[] = ['label' => 'Dấu hiệu: Logo TikTok', 'tone' => 'yellow', 'emoji' => '🟡'];
        }

        if ($this->youtube_logo) {
            $items[] = ['label' => 'Dấu hiệu: Logo YouTube', 'tone' => 'yellow', 'emoji' => '🟡'];
        }

        $risk = strtolower((string) ($this->copyright_risk ?? 'none'));

        if ($risk === 'high') {
            $items[] = ['label' => 'AI nghi ngờ cao – cần admin xác minh', 'tone' => 'red', 'emoji' => '🔴'];
        } elseif ($risk === 'medium') {
            $items[] = ['label' => 'Nên xem lại video', 'tone' => 'orange', 'emoji' => '🟠'];
        } elseif ($risk === 'low') {
            $items[] = ['label' => 'Có dấu hiệu cần kiểm tra', 'tone' => 'yellow', 'emoji' => '🟡'];
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
                'labels'    => $labels,
                'reason'    => trim((string) ($detail['reason'] ?? '')),
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
            $labels[] = 'Phát hiện: Bạo lực';
        }

        if (! empty($detail['adult'])) {
            $labels[] = 'Phát hiện: Nội dung 18+';
        }

        if (! empty($detail['weapon'])) {
            $labels[] = 'Phát hiện: Vũ khí';
        }

        if (! empty($detail['watermark'])) {
            $labels[] = 'Dấu hiệu: Watermark bên thứ ba';
        }

        if (! empty($detail['tiktok_logo'])) {
            $labels[] = 'Dấu hiệu: Logo TikTok';
        }

        if (! empty($detail['youtube_logo'])) {
            $labels[] = 'Dấu hiệu: Logo YouTube';
        }

        $risk = strtolower((string) ($detail['copyright_risk'] ?? 'none'));

        if ($risk === 'high') {
            $labels[] = 'Mức nghi ngờ: Cao – AI nghi ngờ phát lại từ nguồn khác';
        } elseif ($risk === 'medium') {
            $labels[] = 'Mức nghi ngờ: Trung bình – Nên xem lại';
        } elseif ($risk === 'low') {
            $labels[] = 'Mức nghi ngờ: Thấp – Có dấu hiệu cần kiểm tra';
        }

        return $labels;
    }
}
