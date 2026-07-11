<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReviewItem extends Model
{
    public const STATUS_PASS = 'pass';

    public const STATUS_FAIL = 'fail';

    public const ITEM_COURSE_INFORMATION = 'course_information';

    public const ITEM_THUMBNAIL = 'thumbnail';

    public const ITEM_DESCRIPTION = 'description';

    public const ITEM_OBJECTIVES = 'objectives';

    public const ITEM_CATEGORY = 'category';

    public const ITEM_PRICE = 'price';

    public const ITEM_LESSON_COUNT = 'lesson_count';

    public const ITEM_VIDEO_DURATION = 'video_duration';

    public const ITEM_VIDEO_QUALITY = 'video_quality';

    public const ITEM_ATTACHMENTS = 'attachments';

    public const ITEM_COPYRIGHT = 'copyright';

    public const ITEM_KEYS = [
        self::ITEM_COURSE_INFORMATION,
        self::ITEM_THUMBNAIL,
        self::ITEM_DESCRIPTION,
        self::ITEM_OBJECTIVES,
        self::ITEM_CATEGORY,
        self::ITEM_PRICE,
        self::ITEM_LESSON_COUNT,
        self::ITEM_VIDEO_DURATION,
        self::ITEM_VIDEO_QUALITY,
        self::ITEM_ATTACHMENTS,
        self::ITEM_COPYRIGHT,
    ];

    public const ADMIN_CHECKLIST_KEYS = [
        self::ITEM_COURSE_INFORMATION,
        self::ITEM_THUMBNAIL,
        self::ITEM_DESCRIPTION,
        self::ITEM_OBJECTIVES,
        self::ITEM_LESSON_COUNT,
        self::ITEM_VIDEO_DURATION,
        self::ITEM_VIDEO_QUALITY,
        self::ITEM_ATTACHMENTS,
        self::ITEM_COPYRIGHT,
    ];

    public const ITEM_LABELS = [
        self::ITEM_COURSE_INFORMATION => 'Thông tin khóa học',
        self::ITEM_THUMBNAIL => 'Ảnh thumbnail',
        self::ITEM_DESCRIPTION => 'Mô tả',
        self::ITEM_OBJECTIVES => 'Mục tiêu',
        self::ITEM_CATEGORY => 'Danh mục',
        self::ITEM_PRICE => 'Giá',
        self::ITEM_LESSON_COUNT => 'Số bài học',
        self::ITEM_VIDEO_DURATION => 'Thời lượng video',
        self::ITEM_VIDEO_QUALITY => 'Chất lượng video',
        self::ITEM_ATTACHMENTS => 'Tài liệu đính kèm',
        self::ITEM_COPYRIGHT => 'Bản quyền',
    ];

    protected $fillable = [
        'course_review_id',
        'item_key',
        'status',
        'note',
    ];

    public function courseReview(): BelongsTo
    {
        return $this->belongsTo(CourseReview::class);
    }

    public function itemLabel(): string
    {
        return self::ITEM_LABELS[$this->item_key] ?? $this->item_key;
    }

    public function isPass(): bool
    {
        return $this->status === self::STATUS_PASS;
    }
}
