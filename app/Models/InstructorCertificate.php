<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InstructorCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requirement_id',
        'source_type',
        'instructor_teaching_field_id',
        'file_path',
        'document_url',
        'original_name',
        'mime_type',
        'file_size',
        'title',
        'document_type',
        'verification_method',
        'diploma_number',
        'book_reg_number',
        'lookup_url',
        'supplementary_proof_type',
        'supplementary_file_path',
        'credential_url',
        'status',
        'rejection_reason',
        'uploaded_at',
        'reviewed_at',
        'reviewed_by',
    ];

    public static function documentTypeLabels(): array
    {
        return [
            'certificate' => 'Chứng chỉ',
            'degree' => 'Bằng cấp',
            'employment_contract' => 'Hợp đồng lao động',
            'transcript' => 'Bảng điểm',
            'employment_confirmation' => 'Giấy xác nhận công tác',
            'portfolio' => 'Hồ sơ năng lực',
            'other' => 'Tài liệu minh chứng khác',
        ];
    }

    public function documentTypeLabel(): string
    {
        $types = static::documentTypeLabels();

        return $types[$this->document_type] ?? 'Chứng chỉ';
    }

    public static function supplementaryProofTypeLabels(): array
    {
        return [
            'notarized' => 'Bản sao công chứng (trong 6 tháng)',
            'transcript' => 'Bảng điểm tốt nghiệp',
            'student_portal' => 'Ảnh cổng thông tin / App trường',
            'capstone_project' => 'Dự án tốt nghiệp / Link Github',
            'center_transcript' => 'Bảng điểm đánh giá của trung tâm',
            'completion_email_or_receipt' => 'Email tốt nghiệp / Hóa đơn học phí',
            'contract' => 'Hợp đồng lao động',
            'appointment' => 'Quyết định bổ nhiệm / tuyển dụng',
            'vssid' => 'Mã số / Ảnh đóng BHXH (VssID)',
        ];
    }

    public function supplementaryProofTypeLabel(): ?string
    {
        if (! $this->supplementary_proof_type) {
            return null;
        }

        return static::supplementaryProofTypeLabels()[$this->supplementary_proof_type] ?? $this->supplementary_proof_type;
    }

    public function isDomesticCertificate(): bool
    {
        return str_starts_with((string) $this->verification_method, 'domestic_center');
    }

    public function hasAntiForgeryVerification(): bool
    {
        $isDegree = $this->document_type === 'degree';
        $isCert = $this->document_type === 'certificate';

        if (! $isDegree && ! $isCert) {
            $reqTitle = $this->relationLoaded('requirement') ? $this->requirement?->document_title : null;
            $titleToCheck = mb_strtolower(implode(' ', array_filter([$this->title, $this->original_name, $reqTitle])));
            $isDegree = Str::contains($titleToCheck, ['bằng', 'đại học', 'cao đẳng']);
            $isCert = ! $isDegree && Str::contains($titleToCheck, ['chứng chỉ']);
        }

        if ($isDegree) {
            $hasLookup = filled($this->diploma_number) && filled($this->book_reg_number);
            $hasSupplementary = filled($this->supplementary_file_path);

            return $hasLookup || $hasSupplementary;
        }

        if ($isCert) {
            $hasCredential = filled($this->credential_url);
            $hasCenterLookup = filled($this->diploma_number) || filled($this->lookup_url);
            $hasSupplementary = filled($this->supplementary_file_path);

            return $hasCredential || $hasCenterLookup || $hasSupplementary;
        }

        return true;
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: ($this->relationLoaded('requirement') ? $this->requirement?->document_title : null) ?: $this->original_name ?: 'Tài liệu';
    }

    public function getNameAttribute(): ?string
    {
        return $this->title ?: $this->original_name;
    }

    public function getIssuedAtAttribute(): ?Carbon
    {
        return $this->reviewed_at ?: $this->uploaded_at ?: $this->created_at;
    }

    public function getInstitutionAttribute(): ?string
    {
        return null;
    }

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(InstructorDocumentRequirement::class, 'requirement_id');
    }

    public function teachingField(): BelongsTo
    {
        return $this->belongsTo(InstructorTeachingField::class, 'instructor_teaching_field_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes > 0 ? $bytes.' B' : 'N/A';
    }

    public function isUrlSource(): bool
    {
        return $this->source_type === 'url';
    }

    public function sourceLabel(): string
    {
        return $this->isUrlSource() ? 'URL' : 'File upload';
    }

    public function isImage(): bool
    {
        return in_array(strtolower($this->mime_type ?? ''), ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'], true)
            || preg_match('/\.(jpg|jpeg|png|webp)$/i', (string) $this->file_path);
    }

    public function isPdf(): bool
    {
        return strtolower($this->mime_type ?? '') === 'application/pdf'
            || preg_match('/\.pdf$/i', (string) $this->file_path);
    }

    public function isVideo(): bool
    {
        return str_starts_with(strtolower((string) $this->mime_type), 'video/')
            || preg_match('/\.(mp4|mov|webm)$/i', (string) ($this->original_name ?: $this->file_path));
    }
}
