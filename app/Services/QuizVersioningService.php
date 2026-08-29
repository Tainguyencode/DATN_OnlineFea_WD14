<?php

namespace App\Services;

use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizVersioningService
{
    public function ensureInitialDraft(Quiz $quiz, ?User $creator = null): QuizVersion
    {
        return DB::transaction(function () use ($quiz, $creator): QuizVersion {
            $lockedQuiz = $this->lockedQuiz($quiz);
            $this->assertPointersBelongToQuiz($lockedQuiz);

            if ($lockedQuiz->current_draft_version_id) {
                return $this->currentDraft($lockedQuiz);
            }

            if ($lockedQuiz->current_published_version_id) {
                throw ValidationException::withMessages([
                    'quiz' => 'Quiz đã có phiên bản xuất bản; bản nháp chỉ được tạo khi có thay đổi nội dung.',
                ]);
            }

            if ($lockedQuiz->versions()->exists()) {
                throw ValidationException::withMessages([
                    'quiz' => 'Quiz đã có lịch sử phiên bản nhưng không có con trỏ hiện tại hợp lệ.',
                ]);
            }

            $draft = $lockedQuiz->versions()->create([
                ...$this->metadataFromQuiz($lockedQuiz),
                'version' => 1,
                'status' => QuizVersion::STATUS_DRAFT,
                'created_by' => $creator?->id,
            ]);

            $lockedQuiz->update(['current_draft_version_id' => $draft->id]);

            return $draft->fresh();
        });
    }

    public function ensureDraft(Quiz $quiz, ?User $creator = null): QuizVersion
    {
        return DB::transaction(function () use ($quiz, $creator): QuizVersion {
            $lockedQuiz = $this->lockedQuiz($quiz);
            $this->assertPointersBelongToQuiz($lockedQuiz);

            if ($lockedQuiz->current_draft_version_id) {
                $draft = $this->currentDraft($lockedQuiz);
                $this->assertDraftEditable($lockedQuiz, $draft);

                return $draft;
            }

            if (! $lockedQuiz->current_published_version_id) {
                return $this->ensureInitialDraft($lockedQuiz, $creator);
            }

            $published = $this->currentPublished($lockedQuiz);
            $nextVersion = ((int) $lockedQuiz->versions()->max('version')) + 1;
            $draft = $lockedQuiz->versions()->create([
                'version' => $nextVersion,
                'title' => $published->title,
                'description' => $published->description,
                'pass_score' => $published->pass_score,
                'time_limit_minutes' => $published->time_limit_minutes,
                'max_attempts' => $published->max_attempts,
                'question_count' => $published->question_count,
                'status' => QuizVersion::STATUS_DRAFT,
                'created_by' => $creator?->id,
            ]);

            $published->questionMappings()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->each(function (QuizVersionQuestion $mapping) use ($draft): void {
                    $draft->questionMappings()->create([
                        'question_id' => $mapping->question_id,
                        'question_version_id' => $mapping->question_version_id,
                        'sort_order' => $mapping->sort_order,
                    ]);
                });

            $lockedQuiz->update(['current_draft_version_id' => $draft->id]);
            $this->recordCandidateUpdate($lockedQuiz->fresh(), $draft);

            return $draft->fresh();
        });
    }

    public function candidateVersion(Quiz $quiz): ?QuizVersion
    {
        $this->assertPointersBelongToQuiz($quiz);

        if ($quiz->current_draft_version_id) {
            return $this->currentDraft($quiz);
        }

        return $quiz->current_published_version_id ? $this->currentPublished($quiz) : null;
    }

    public function authoringVersion(Quiz $quiz): ?QuizVersion
    {
        return $this->candidateVersion($quiz);
    }

    public function currentPublished(Quiz $quiz): QuizVersion
    {
        $version = QuizVersion::query()->find($quiz->current_published_version_id);

        if (! $version) {
            throw ValidationException::withMessages(['quiz' => 'Không tìm thấy phiên bản Quiz đang áp dụng.']);
        }

        $this->assertVersionBelongsToQuiz($quiz, $version);

        if ($version->status !== QuizVersion::STATUS_PUBLISHED) {
            throw ValidationException::withMessages(['quiz' => 'Con trỏ phiên bản đang áp dụng không trỏ tới nội dung bất biến.']);
        }

        return $version;
    }

    public function currentDraft(Quiz $quiz): QuizVersion
    {
        $version = QuizVersion::query()->find($quiz->current_draft_version_id);

        if (! $version) {
            throw ValidationException::withMessages(['quiz' => 'Không tìm thấy bản nháp Quiz hiện tại.']);
        }

        $this->assertVersionBelongsToQuiz($quiz, $version);

        if ($version->status !== QuizVersion::STATUS_DRAFT) {
            throw ValidationException::withMessages(['quiz' => 'Con trỏ bản nháp không trỏ tới một QuizVersion có thể chỉnh sửa.']);
        }

        return $version;
    }

    public function assertPointersBelongToQuiz(Quiz $quiz): void
    {
        foreach (array_filter([
            $quiz->current_published_version_id,
            $quiz->current_draft_version_id,
        ]) as $versionId) {
            $version = QuizVersion::query()->find($versionId);

            if (! $version) {
                throw ValidationException::withMessages(['quiz' => 'Con trỏ phiên bản Quiz không hợp lệ.']);
            }

            $this->assertVersionBelongsToQuiz($quiz, $version);
        }
    }

    public function assertVersionBelongsToQuiz(Quiz $quiz, QuizVersion $version): void
    {
        if ((int) $version->quiz_id !== (int) $quiz->id) {
            throw ValidationException::withMessages([
                'quiz' => 'Phiên bản được chọn không thuộc Quiz này.',
            ]);
        }
    }

    public function assertMutableQuizVersion(QuizVersion $version): void
    {
        if ($version->status !== QuizVersion::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'quiz' => 'Không thể sửa trực tiếp phiên bản Quiz đã xuất bản hoặc đã thay thế.',
            ]);
        }
    }

    public function assertMutableQuestionVersion(QuestionVersion $version, QuizVersion $currentDraft): void
    {
        $immutableReferenceExists = $version->quizVersionMappings()
            ->whereHas('quizVersion', fn ($query) => $query->whereIn('status', [
                QuizVersion::STATUS_PUBLISHED,
                QuizVersion::STATUS_SUPERSEDED,
            ]))
            ->exists();
        $otherCompositionExists = $version->quizVersionMappings()
            ->where('quiz_version_id', '!=', $currentDraft->id)
            ->exists();

        if ($version->status !== QuestionVersion::STATUS_DRAFT || $immutableReferenceExists || $otherCompositionExists) {
            throw ValidationException::withMessages([
                'question' => 'Không thể sửa trực tiếp phiên bản câu hỏi đã được chia sẻ hoặc xuất bản.',
            ]);
        }
    }

    /**
     * @return array{version: QuestionVersion, option_map: array<int, int>, source_version: QuestionVersion}
     */
    public function ensureMutableQuestionVersion(Quiz $quiz, QuizQuestion $question): array
    {
        return DB::transaction(function () use ($quiz, $question): array {
            $lockedQuiz = $this->lockedQuiz($quiz);

            if ((int) $question->quiz_id !== (int) $lockedQuiz->id) {
                throw ValidationException::withMessages(['question' => 'Câu hỏi không thuộc Quiz này.']);
            }

            $draft = $this->ensureDraft($lockedQuiz, auth()->user());
            $this->assertDraftEditable($lockedQuiz, $draft);
            $mapping = $draft->questionMappings()
                ->where('question_id', $question->id)
                ->lockForUpdate()
                ->first();

            if (! $mapping) {
                throw ValidationException::withMessages([
                    'question' => 'Câu hỏi không thuộc bản nháp Quiz hiện tại.',
                ]);
            }

            $source = QuestionVersion::query()->with('options')->lockForUpdate()->findOrFail($mapping->question_version_id);
            $isImmutable = $source->status !== QuestionVersion::STATUS_DRAFT
                || $source->quizVersionMappings()
                    ->whereHas('quizVersion', fn ($query) => $query->whereIn('status', [
                        QuizVersion::STATUS_PUBLISHED,
                        QuizVersion::STATUS_SUPERSEDED,
                    ]))
                    ->exists()
                || $source->quizVersionMappings()->where('quiz_version_id', '!=', $draft->id)->exists();

            if (! $isImmutable) {
                return [
                    'version' => $source,
                    'option_map' => $source->options->mapWithKeys(fn (QuizOption $option) => [$option->id => $option->id])->all(),
                    'source_version' => $source,
                ];
            }

            QuizQuestion::query()->whereKey($question->id)->lockForUpdate()->firstOrFail();
            $nextVersion = ((int) $question->versions()->max('version')) + 1;
            $clone = $question->versions()->create([
                'version' => $nextVersion,
                'question' => $source->question,
                'image_path' => $source->image_path,
                'type' => $source->type,
                'points' => $source->points,
                'explanation' => $source->explanation,
                'status' => QuestionVersion::STATUS_DRAFT,
            ]);
            $optionMap = [];

            foreach ($source->options as $option) {
                $clonedOption = $clone->options()->create([
                    'quiz_question_id' => $question->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'sort_order' => $option->sort_order,
                ]);
                $optionMap[$option->id] = $clonedOption->id;
            }

            $mapping->update(['question_version_id' => $clone->id]);
            $this->recordCandidateUpdate($lockedQuiz->fresh(), $draft);

            return [
                'version' => $clone->fresh('options'),
                'option_map' => $optionMap,
                'source_version' => $source,
            ];
        });
    }

    public function mappingForQuestion(QuizVersion $version, QuizQuestion $question): QuizVersionQuestion
    {
        $mapping = $version->questionMappings()->where('question_id', $question->id)->first();

        if (! $mapping) {
            throw ValidationException::withMessages([
                'question' => 'Câu hỏi không thuộc phiên bản Quiz hiện tại.',
            ]);
        }

        return $mapping;
    }

    public function moveQuestion(QuizVersion $draft, QuizQuestion $question, int $requestedPosition): void
    {
        DB::transaction(function () use ($draft, $question, $requestedPosition): void {
            $this->assertMutableQuizVersion($draft);
            $mappings = $draft->questionMappings()->lockForUpdate()->get();
            $target = $mappings->firstWhere('question_id', $question->id);

            if (! $target) {
                throw ValidationException::withMessages(['question' => 'Câu hỏi không thuộc bản nháp hiện tại.']);
            }

            $ordered = $mappings->reject(fn (QuizVersionQuestion $mapping) => $mapping->is($target))->values();
            $position = min(max(0, $requestedPosition), $ordered->count());
            $ordered->splice($position, 0, [$target]);
            $offset = ((int) $mappings->max('sort_order')) + $ordered->count() + 1000;

            foreach ($ordered as $index => $mapping) {
                $mapping->update(['sort_order' => $offset + $index]);
            }

            foreach ($ordered as $index => $mapping) {
                $mapping->update(['sort_order' => $index]);
            }
        });
    }

    public function projectVersion(Quiz $quiz, QuizVersion $version): Quiz
    {
        $this->assertVersionBelongsToQuiz($quiz, $version);
        $version->loadMissing([
            'questionMappings.question',
            'questionMappings.questionVersion.options',
            'questionMappings.invalidations',
        ]);
        $projected = clone $quiz;
        $projected->setAttribute('title', $version->title);
        $projected->setAttribute('description', $version->description);
        $projected->setAttribute('pass_score', $version->pass_score);
        $projected->setAttribute('time_limit_minutes', $version->time_limit_minutes);
        $projected->setAttribute('max_attempts', $version->max_attempts);
        $projected->setAttribute('question_count', $version->question_count);

        $questions = $version->questionMappings->map(function (QuizVersionQuestion $mapping): QuizQuestion {
            $identity = clone $mapping->question;
            $questionVersion = $mapping->questionVersion;
            $identity->setAttribute('question', $questionVersion->question);
            $identity->setAttribute('image_path', $questionVersion->image_path);
            $identity->setAttribute('type', $questionVersion->type);
            $identity->setAttribute('points', $questionVersion->points);
            $identity->setAttribute('explanation', $questionVersion->explanation);
            $identity->setAttribute('sort_order', $mapping->sort_order);
            $identity->setRelation('options', $questionVersion->options);
            $identity->setRelation('authoringVersion', $questionVersion);
            $identity->setRelation('versionMapping', $mapping);

            return $identity;
        });

        $projected->setRelation('questions', new EloquentCollection($questions->all()));
        $projected->setRelation('displayVersion', $version);

        return $projected;
    }

    public function recordCandidateUpdate(Quiz $quiz, QuizVersion $draft, ?bool $desiredIsActive = null): ?ContentUpdate
    {
        $this->assertVersionBelongsToQuiz($quiz, $draft);

        if (! $quiz->current_published_version_id) {
            return null;
        }

        $course = $quiz->lesson()->with('course')->first()?->course;

        if (! $course || ! $this->courseHasPublishedContent($course)) {
            return null;
        }

        $existing = $this->contentUpdateForVersion($quiz, $draft);

        if ($existing && in_array($existing->status, [ContentUpdate::STATUS_PENDING, ContentUpdate::STATUS_APPROVED], true)) {
            throw ValidationException::withMessages([
                'quiz' => $existing->status === ContentUpdate::STATUS_PENDING
                    ? 'Bản nháp Quiz đang chờ duyệt và tạm thời không thể chỉnh sửa.'
                    : 'Bản Quiz đã được duyệt và đang chờ kích hoạt an toàn ở Phase 2B0.8.',
            ]);
        }

        $payload = [
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $draft->id,
            'desired_is_active' => $desiredIsActive ?? (bool) $quiz->is_active,
            'activation_deferred' => true,
        ];

        if ($existing) {
            $existing->update([
                'payload' => $payload,
                'status' => ContentUpdate::STATUS_DRAFT,
                'rejection_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'submitted_at' => null,
            ]);

            return $existing->fresh();
        }

        return ContentUpdate::create([
            'type' => ContentUpdate::TYPE_QUIZ,
            'entity_id' => $quiz->id,
            'course_id' => $course->id,
            'action' => ContentUpdate::ACTION_UPDATE,
            'payload' => $payload,
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => auth()->id() ?? $course->instructor_id,
        ]);
    }

    public function contentUpdateForVersion(Quiz $quiz, QuizVersion $version): ?ContentUpdate
    {
        return ContentUpdate::query()
            ->where('type', ContentUpdate::TYPE_QUIZ)
            ->where('entity_id', $quiz->id)
            ->latest('id')
            ->get()
            ->first(fn (ContentUpdate $update): bool => (int) data_get($update->payload, 'quiz_version_id') === (int) $version->id);
    }

    public function assertDraftEditable(Quiz $quiz, QuizVersion $draft): void
    {
        $this->assertMutableQuizVersion($draft);
        $update = $this->contentUpdateForVersion($quiz, $draft);

        if ($update?->isPending()) {
            throw ValidationException::withMessages(['quiz' => 'Bản nháp Quiz đang chờ duyệt và không thể chỉnh sửa.']);
        }

        if ($update?->isApproved()) {
            throw ValidationException::withMessages(['quiz' => 'Bản Quiz đã duyệt đang chờ kích hoạt an toàn ở Phase 2B0.8.']);
        }
    }

    public function publishDraft(Quiz $quiz, QuizVersion $candidate): QuizVersion
    {
        return DB::transaction(function () use ($quiz, $candidate): QuizVersion {
            $lockedQuiz = $this->lockedQuiz($quiz);
            $this->assertPointersBelongToQuiz($lockedQuiz);
            $this->assertVersionBelongsToQuiz($lockedQuiz, $candidate);

            if ($lockedQuiz->current_published_version_id) {
                throw ValidationException::withMessages([
                    'quiz' => 'Không thể kích hoạt Quiz V2 trước khi attempt binding của Phase 2B0.8 hoàn tất.',
                ]);
            }

            if ((int) $lockedQuiz->current_draft_version_id !== (int) $candidate->id) {
                throw ValidationException::withMessages(['quiz' => 'Ứng viên xuất bản không phải bản nháp hiện tại.']);
            }

            $candidate = QuizVersion::query()->lockForUpdate()->findOrFail($candidate->id);
            $validation = app(QuizContentService::class)->validateQuizVersion($candidate);

            if (! $validation['is_complete']) {
                throw ValidationException::withMessages(['quiz' => $validation['errors']]);
            }

            $candidate->questionMappings()->lockForUpdate()->get();
            $questionVersionIds = $candidate->questionMappings()->pluck('question_version_id');
            QuestionVersion::query()
                ->whereIn('id', $questionVersionIds)
                ->where('status', QuestionVersion::STATUS_DRAFT)
                ->update([
                    'status' => QuestionVersion::STATUS_PUBLISHED,
                    'published_at' => now(),
                ]);
            $candidate->update([
                'status' => QuizVersion::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);
            $lockedQuiz->update([
                'current_published_version_id' => $candidate->id,
                'current_draft_version_id' => null,
            ]);

            return $candidate->fresh();
        });
    }

    public function publishInitialCourseDrafts(Course $course): void
    {
        $course->lessons()
            ->whereHas('quiz')
            ->with('quiz')
            ->get()
            ->each(function ($lesson): void {
                $quiz = $lesson->quiz;

                if (! $quiz->current_published_version_id && $quiz->current_draft_version_id) {
                    $this->publishDraft($quiz, $this->currentDraft($quiz));
                }
            });
    }

    private function lockedQuiz(Quiz $quiz): Quiz
    {
        return Quiz::query()->lockForUpdate()->findOrFail($quiz->id);
    }

    /** @return array<string, mixed> */
    private function metadataFromQuiz(Quiz $quiz): array
    {
        return [
            'title' => $quiz->title,
            'description' => $quiz->description,
            'pass_score' => $quiz->pass_score,
            'time_limit_minutes' => $quiz->time_limit_minutes,
            'max_attempts' => $quiz->max_attempts,
            'question_count' => $quiz->question_count,
        ];
    }

    private function courseHasPublishedContent(Course $course): bool
    {
        return (bool) $course->is_published || in_array($course->status, [
            Course::STATUS_APPROVED,
            Course::STATUS_PUBLISHED,
            Course::STATUS_PENDING_UPDATE,
            Course::STATUS_REJECTED_UPDATE,
        ], true);
    }
}
