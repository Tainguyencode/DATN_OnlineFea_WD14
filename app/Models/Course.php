<?php

namespace App\Models;

use App\Data\CourseSubmissionCheckResult;
use App\Enums\CourseStatus;
use App\Services\ContentUpdateService;
use App\Services\CourseSubmissionValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Model Quản lý Khóa học Trực tuyến (Course Model)
 *
 * Đại diện cho một khóa học trên hệ thống LMS với đầy đủ thông tin:
 * - Thông tin cơ bản: Tên khóa học, mô tả, ảnh thumbnail, video preview, danh mục, giảng viên tạo.
 * - Tài chính & Giá bán: Giá gốc (`price`), giá khuyến mãi (`discount_price`), giá hiệu lực (`effective_price`).
 * - Phê duyệt & Trạng thái: Nháp (`draft`), Chờ duyệt (`pending_review`), Đã duyệt (`approved`), Đã xuất bản (`published`), Từ chối (`rejected`).
 * - Điều kiện hoàn thành: Tỷ lệ xem video (`required_video_percent`), tỷ lệ bài học (`required_lesson_percent`), điểm trắc nghiệm tối thiểu (`minimum_quiz_score`).
 * - Quan hệ: Bài học (`lessons`), Chương/Mục (`chapters`/`sections`), Ghi danh (`enrollments`), Đánh giá (`reviews`), Nhóm học tập (`studyGroups`).
 */
class Course extends Model
{
    /** @deprecated Use CourseStatus enum */
    public const STATUS_DRAFT = 'draft';

    /** @deprecated Use CourseStatus::PendingReview */
    public const STATUS_SUBMITTED = 'pending_review';

    /** @deprecated Use CourseStatus::PendingReview */
    public const STATUS_PENDING = 'pending_review';

    public const STATUS_NEED_REVISION = 'rejected';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_PENDING_UPDATE = 'pending_update';

    public const STATUS_REJECTED_UPDATE = 'rejected_update';

    public const STATUSES = [
        'draft',
        'pending_review',
        'approved',
        'rejected',
        'published',
        'suspended',
        'archived',
        'pending_update',
        'rejected_update',
    ];

    /** Nhãn tiếng Việt cho từng trạng thái */
    public const STATUS_LABELS = [
        'draft' => 'Nháp',
        'pending_review' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'published' => 'Đã xuất bản',
        'rejected' => 'Bị từ chối',
        'suspended' => 'Tạm ngừng',
        'archived' => 'Đã lưu trữ',
        'pending_update' => 'Cập nhật chờ duyệt',
        'rejected_update' => 'Bị từ chối cập nhật',
    ];

    /** Số lượng bài học tối thiểu bắt buộc để có thể nộp duyệt */
    public const MIN_LESSON_COUNT = 5;

    /** Tổng thời lượng video tối thiểu (phút) bắt buộc để có thể nộp duyệt */
    public const MIN_VIDEO_DURATION_MINUTES = 30;

    protected $fillable = [
        'instructor_id', 'category_id', 'title', 'slug', 'short_description',
        'description', 'objectives', 'target_audience', 'requirements',
        'thumbnail', 'preview_video', 'price',
        'discount_price', 'sale_price', 'level', 'language', 'status', 'is_published',
        'reject_reason', 'rejection_reason', 'rating_avg', 'rating_count',
        'enrollment_count', 'duration_minutes', 'tags', 'is_featured',
        'published_at', 'submitted_at', 'approved_at', 'suspended_at', 'submission_count',
        'required_video_percent', 'required_lesson_percent', 'minimum_quiz_score',
        'require_all_quizzes', 'require_all_assignments', 'certificate_enabled',
        'copyright_agreed', 'copyright_agreed_at', 'copyright_agreed_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating_avg' => 'decimal:2',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'require_all_quizzes' => 'boolean',
            'require_all_assignments' => 'boolean',
            'certificate_enabled' => 'boolean',
            'copyright_agreed' => 'boolean',
            'published_at' => 'datetime',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
            'copyright_agreed_at' => 'datetime',
        ];
    }

    /** Giảng viên sở hữu khóa học này */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /** Người xác nhận bản quyền khóa học */
    public function copyrightAgreedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'copyright_agreed_by');
    }

    /** Danh mục ngành học của khóa học */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Danh sách các chương học */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }

    /** Danh sách các mục chương học (Sections) */
    public function courseSections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    /** Danh sách tất cả các bài học trong khóa học */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    /** Danh sách các bản ghi ghi danh của học viên */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** Các nhóm học tập thuộc khóa học */
    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class);
    }

    /** Danh sách yêu thích của học viên */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /** Danh sách học viên đã thả tim / thêm vào yêu thích */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    /** Danh sách đánh giá khóa học */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Danh sách các cuộc trao đổi trong khóa học */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    /** Danh sách đánh giá công khai đã được hiển thị */
    public function approvedReviews(): HasMany
    {
        return $this->visibleReviews();
    }

    public function visibleReviews(): HasMany
    {
        return $this->reviews()->visible();
    }

    /** Lịch sử các lần duyệt khóa học từ Admin */
    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class)->orderByDesc('submission_number');
    }

    public function latestCourseReview(): ?CourseReview
    {
        return $this->courseReviews()->first();
    }

    /** Danh sách bài tập tự luận */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Lấy giá thực tế của khóa học (ưu tiên giá khuyến mãi nếu có).
     */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->sale_price ?? $this->price);
    }

    /**
     * Lấy đường dẫn ảnh đại diện (thumbnail) hợp lệ của khóa học.
     */
    public function thumbnailUrl(): string
    {
        if (! $this->thumbnail) {
            return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80';
        }

        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }

        if (Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/'.$this->thumbnail);
        }

        return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=80';
    }

    /**
     * Kiểm tra xem Giảng viên này có phải là người sở hữu khóa học hay không.
     */
    public function isOwnedBy(User $user): bool
    {
        return (int) $this->instructor_id === (int) $user->id;
    }

    /**
     * Kiểm tra xem khóa học đã được Admin duyệt nội dung hay chưa (độc lập với hồ sơ giảng viên).
     */
    public function isContentApproved(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_PUBLISHED,
            self::STATUS_PENDING_UPDATE,
            self::STATUS_REJECTED_UPDATE,
        ], true) || (bool) $this->is_published || $this->approved_at !== null;
    }

    /**
     * Kiểm tra xem khóa học có đủ điều kiện hiển thị công khai cho học viên hay không:
     * 1. Khóa học đã được Admin duyệt nội dung (và không ở trạng thái nháp, từ chối, tạm dừng, lưu trữ).
     * 2. Giảng viên sở hữu đã được Admin duyệt hồ sơ (instructor_status === 'approved').
     * 3. Giảng viên sở hữu không bị khóa tài khoản và đang hoạt động.
     */
    public function isPublished(): bool
    {
        $hasApprovedContent = in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_PUBLISHED,
            self::STATUS_PENDING_UPDATE,
            self::STATUS_REJECTED_UPDATE,
        ], true) || (bool) $this->is_published;

        if (! $hasApprovedContent) {
            return false;
        }

        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING, self::STATUS_REJECTED, self::STATUS_SUSPENDED ?? 'suspended', self::STATUS_ARCHIVED], true) && ! (bool) $this->is_published) {
            return false;
        }

        $instructor = $this->relationLoaded('instructor') ? $this->instructor : $this->instructor()->first();
        if (! $instructor) {
            return false;
        }

        $isInstructorApproved = $instructor->instructor_status === 'approved';
        $isInstructorActive = (bool) $instructor->is_active && ! $instructor->isLocked();

        return $isInstructorApproved && $isInstructorActive;
    }

    /**
     * Scope lấy các khóa học đang được hiển thị công khai cho học viên.
     * Áp dụng điều kiện Course approved + Instructor approved + Account active.
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->where('courses.is_published', true)
                ->orWhereIn('courses.status', [
                    self::STATUS_PUBLISHED,
                    self::STATUS_APPROVED,
                    self::STATUS_PENDING_UPDATE,
                    self::STATUS_REJECTED_UPDATE,
                ]);
        })->whereNotIn('courses.status', [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_REJECTED,
            'suspended',
            self::STATUS_ARCHIVED,
        ])->whereHas('instructor', function ($iq) {
            $iq->where('instructor_status', 'approved')
                ->where('is_active', true)
                ->where(function ($aq) {
                    $aq->whereNull('account_status')
                        ->orWhereNotIn('account_status', ['locked', 'suspended']);
                });
        });
    }

    /**
     * Kiểm tra xem giảng viên có thể chỉnh sửa khóa học hay không.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED, self::STATUS_APPROVED, self::STATUS_PUBLISHED, self::STATUS_REJECTED_UPDATE], true);
    }

    public function statusEnum(): CourseStatus
    {
        return CourseStatus::from($this->status);
    }

    /**
     * Kiểm tra người dùng hiện tại đã thêm khóa học này vào danh sách yêu thích hay chưa.
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->wishlists()
            ->where('user_id', $user->id)
            ->exists();
    }

    /** Lý do từ chối phê duyệt (nếu có) */
    public function rejectionReasonText(): ?string
    {
        return $this->reject_reason ?: $this->rejection_reason;
    }

    /** Tên tiếng Việt hiển thị của trạng thái */
    public function statusLabel(): string
    {
        if ($this->status === self::STATUS_APPROVED) {
            $instructor = $this->relationLoaded('instructor') ? $this->instructor : $this->instructor()->first();
            if ($instructor && $instructor->instructor_status !== 'approved') {
                return 'Đã duyệt nội dung (Chờ duyệt GV)';
            }

            return 'Đã duyệt';
        }

        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /** Kiểm tra giảng viên có được phép gửi yêu cầu phê duyệt khóa học hay không */
    public function canBeSubmittedForReview(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_REJECTED,
            self::STATUS_PUBLISHED,
            self::STATUS_PENDING_UPDATE,
            self::STATUS_REJECTED_UPDATE,
        ], true);
    }

    /** Kiểm tra khóa học có đang chờ Admin phê duyệt hay không */
    public function isAwaitingAdminReview(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_PENDING_UPDATE], true);
    }

    /** Tính tổng thời lượng video (giây) của tất cả bài học */
    public function totalVideoDurationSeconds(): int
    {
        $sections = app(ContentUpdateService::class)->mergeCurriculumWithUpdates($this);

        $totalSeconds = 0;
        foreach ($sections as $section) {
            foreach ($section->lessons as $lesson) {
                if (! empty($lesson->is_pending_deletion)) {
                    continue;
                }

                if (! $lesson->hasVideoSource()) {
                    continue;
                }

                $totalSeconds += $lesson->effectiveDurationSeconds();
            }
        }

        return $totalSeconds;
    }

    /** Tính tổng thời lượng video (phút) */
    public function totalVideoDurationMinutes(): int
    {
        return (int) floor($this->totalVideoDurationSeconds() / 60);
    }

    /** Tổng số bài học */
    public function lessonCount(): int
    {
        $sections = app(ContentUpdateService::class)->mergeCurriculumWithUpdates($this);

        $count = 0;
        foreach ($sections as $section) {
            foreach ($section->lessons as $lesson) {
                if (! empty($lesson->is_pending_deletion)) {
                    continue;
                }
                $count++;
            }
        }

        return $count;
    }

    /** Gọi Validator kiểm tra tính đầy đủ của khóa học trước khi cho nộp duyệt */
    public function submissionCheck(): CourseSubmissionCheckResult
    {
        return app(CourseSubmissionValidator::class)->validate($this);
    }

    /** Kiểm tra xem khóa học đã sẵn sàng để gửi duyệt hay chưa */
    public function isReadyForSubmission(): bool
    {
        return $this->submissionCheck()->passes();
    }

    /**
     * Kiểm tra xem khóa học có bài học video nào chưa xử lý HLS hoàn tất hay không.
     */
    public function hasIncompleteHlsVideos(): bool
    {
        return collect($this->videoReadinessBlockers())
            ->contains(fn (array $blocker): bool => $blocker['state'] !== 'missing_source');
    }

    /**
     * Return the current video blockers shown to an instructor.
     *
     * The curriculum merge deliberately includes only active draft/pending updates,
     * so historical rejected/approved uploads cannot keep a newer ready lesson in a
     * processing state.
     *
     * @return array<int, array{title: string, state: 'missing_source'|'uploading'|'processing'|'failed'}>
     */
    public function videoReadinessBlockers(): array
    {
        $sections = app(ContentUpdateService::class)->mergeCurriculumWithUpdates($this);
        $blockers = [];
        $checkedLessonIds = [];

        foreach ($sections as $section) {
            foreach ($section->lessons as $lesson) {
                if (! empty($lesson->is_pending_deletion)) {
                    continue;
                }

                if ($lesson->type !== Lesson::TYPE_VIDEO) {
                    continue;
                }

                if (! empty($lesson->id) && empty($lesson->is_draft_create)) {
                    $checkedLessonIds[] = $lesson->id;
                }

                $blocker = $this->videoReadinessBlockerFor($lesson);
                if ($blocker) {
                    $blockers[] = $blocker;
                }
            }
        }

        $orphanedLessons = $this->lessons()
            ->where('type', Lesson::TYPE_VIDEO)
            ->whereNotIn('id', $checkedLessonIds)
            ->get();
        foreach ($orphanedLessons as $lesson) {
            $blocker = $this->videoReadinessBlockerFor($lesson);
            if ($blocker) {
                $blockers[] = $blocker;
            }
        }

        return $blockers;
    }

    private function videoReadinessBlockerFor(Lesson $lesson): ?array
    {
        if (! $lesson->hasVideoSource()) {
            return ['title' => $lesson->title, 'state' => 'missing_source'];
        }

        // External video URLs do not enter the local/S3 HLS pipeline. They
        // are already playable and should not block course submission while
        // uploaded sources are still being transcoded.
        if (filled($lesson->video_url)
            && ! filled($lesson->original_video_key)
            && ! filled($lesson->hls_manifest_key)
            && ! filled($lesson->video_path)
            && ! filled($lesson->hls_playlist)
            && ! filled($lesson->hls_path)) {
            return null;
        }

        if ($lesson->isHlsReady()) {
            return null;
        }

        if ($lesson->hasFailedProcessing()) {
            return ['title' => $lesson->title, 'state' => 'failed'];
        }

        return [
            'title' => $lesson->title,
            'state' => $lesson->upload_status === 'pending' ? 'uploading' : 'processing',
        ];
    }

    public function requiredVideoPercent(): int
    {
        return (int) ($this->required_video_percent ?? config('course.default_required_video_percent'));
    }

    public function requiredLessonPercent(): int
    {
        return (int) ($this->required_lesson_percent ?? config('course.default_required_lesson_percent'));
    }

    public function minimumQuizScore(): int
    {
        return (int) ($this->minimum_quiz_score ?? config('course.default_minimum_quiz_score'));
    }

    public function curriculumSections()
    {
        return $this->courseSections->isNotEmpty()
            ? $this->courseSections
            : $this->chapters;
    }

    /** Lấy bài học đầu tiên của khóa học để làm lối vào học ngay */
    public function firstLesson(): ?Lesson
    {
        $this->loadMissing([
            'courseSections' => fn ($q) => $q->orderBy('sort_order'),
            'courseSections.lessons' => fn ($q) => $q->orderBy('sort_order'),
            'chapters' => fn ($q) => $q->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        foreach ($this->curriculumSections() as $section) {
            $lesson = $section->lessons->first();
            if ($lesson) {
                return $lesson;
            }
        }

        return $this->lessons()->orderBy('sort_order')->first();
    }

    /** Đường dẫn tham gia trình phát bài học */
    public function learningEntryUrl(): ?string
    {
        $lesson = $this->firstLesson();

        return $lesson
            ? route('courses.lessons.show', [$this, $lesson])
            : null;
    }

    public function totalLessonsCount(): int
    {
        if ($this->relationLoaded('courseSections') && $this->courseSections->isNotEmpty()) {
            return $this->courseSections->flatMap(fn ($s) => $s->lessons ?? collect())->count();
        }

        if ($this->relationLoaded('chapters') && $this->chapters->isNotEmpty()) {
            return $this->chapters->flatMap(fn ($c) => $c->lessons ?? collect())->count();
        }

        return Lesson::where('course_id', $this->id)->count();
    }
}
