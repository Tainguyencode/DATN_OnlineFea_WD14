<?php

namespace App\Services;

use App\Jobs\RegradeQuizQuestionJob;
use App\Models\QuizAttempt;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\QuizVersionQuestionInvalidation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizQuestionInvalidationService
{
    public function request(QuizVersionQuestion $mapping, User $actor, string $reason): QuizVersionQuestionInvalidation
    {
        $reason = trim($reason);
        $this->assertInstructorOwner($actor);
        $this->assertReason($reason);
        $mapping->loadMissing([
            'quizVersion.quiz.lesson.course',
            'quizVersion.quiz.lesson.section.course',
            'quizVersion.quiz.lesson.chapter.course',
            'questionVersion',
        ]);
        $this->assertPublishedHistoricalMapping($mapping);

        $lesson = $mapping->quizVersion?->quiz?->lesson;
        $course = $lesson?->course ?? $lesson?->section?->course ?? $lesson?->chapter?->course;
        abort_unless($course?->isOwnedBy($actor), 403);

        return DB::transaction(function () use ($mapping, $actor, $reason): QuizVersionQuestionInvalidation {
            $lockedMapping = QuizVersionQuestion::query()->lockForUpdate()->findOrFail($mapping->id);
            $existing = QuizVersionQuestionInvalidation::query()
                ->where('quiz_version_question_id', $lockedMapping->id)
                ->lockForUpdate()
                ->first();

            if ($existing?->status === QuizVersionQuestionInvalidation::STATUS_ACTIVE) {
                throw ValidationException::withMessages([
                    'invalidation' => 'Câu hỏi này đã được hủy và không thể yêu cầu lại.',
                ]);
            }

            if ($existing?->status === QuizVersionQuestionInvalidation::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'invalidation' => 'Yêu cầu hủy câu hỏi này đang chờ admin duyệt.',
                ]);
            }

            $data = [
                'status' => QuizVersionQuestionInvalidation::STATUS_PENDING,
                'requested_by' => $actor->id,
                'invalidated_by' => null,
                'reviewed_by' => null,
                'invalidated_at' => null,
                'reviewed_at' => null,
                'reason' => $reason,
                'rejection_reason' => null,
                'regrade_started_at' => null,
                'regrade_completed_at' => null,
            ];

            return $existing
                ? tap($existing)->update($data)
                : QuizVersionQuestionInvalidation::create([
                    'quiz_version_question_id' => $lockedMapping->id,
                    ...$data,
                ]);
        });
    }

    /** @return array{completed: int, in_progress: int} */
    public function counts(QuizVersionQuestion $mapping): array
    {
        $query = QuizAttempt::query()->where('quiz_version_id', $mapping->quiz_version_id);

        return [
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
        ];
    }

    /** @return array{invalidation: QuizVersionQuestionInvalidation, queued: bool, counts: array{completed: int, in_progress: int}} */
    public function approve(QuizVersionQuestionInvalidation $invalidation, User $admin): array
    {
        $this->assertAdmin($admin);

        $invalidation = DB::transaction(function () use ($invalidation, $admin): QuizVersionQuestionInvalidation {
            $locked = QuizVersionQuestionInvalidation::query()
                ->with([
                    'mapping.quizVersion.quiz.lesson.course',
                    'mapping.questionVersion',
                ])
                ->lockForUpdate()
                ->findOrFail($invalidation->id);

            if ($locked->status !== QuizVersionQuestionInvalidation::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'invalidation' => 'Chỉ yêu cầu đang chờ duyệt mới có thể được phê duyệt.',
                ]);
            }

            $this->assertPublishedHistoricalMapping($locked->mapping);

            $locked->update([
                'status' => QuizVersionQuestionInvalidation::STATUS_ACTIVE,
                'invalidated_by' => $admin->id,
                'reviewed_by' => $admin->id,
                'invalidated_at' => now(),
                'reviewed_at' => now(),
                'regrade_started_at' => now(),
                'regrade_completed_at' => null,
            ]);

            return $locked->fresh('mapping.quizVersion');
        });

        $counts = $this->counts($invalidation->mapping);
        $queued = $this->dispatchOrProcess($invalidation, $counts['completed']);

        return compact('invalidation', 'queued', 'counts');
    }

    public function processApproved(QuizVersionQuestionInvalidation $invalidation): void
    {
        $userIds = app(QuizAttemptRegradeService::class)->processInvalidation($invalidation);
        $invalidation->loadMissing('mapping.quizVersion.quiz');
        $quiz = $invalidation->mapping->quizVersion->quiz;

        foreach ($userIds as $userId) {
            app(LearningProgressService::class)->reconcileQuizAfterRegrade($quiz, (int) $userId);
        }
    }

    public function reject(QuizVersionQuestionInvalidation $invalidation, User $admin, string $reason): QuizVersionQuestionInvalidation
    {
        $this->assertAdmin($admin);
        $reason = trim($reason);
        $this->assertReason($reason);

        return DB::transaction(function () use ($invalidation, $admin, $reason): QuizVersionQuestionInvalidation {
            $locked = QuizVersionQuestionInvalidation::query()->lockForUpdate()->findOrFail($invalidation->id);

            if ($locked->status !== QuizVersionQuestionInvalidation::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'invalidation' => 'Chỉ yêu cầu đang chờ duyệt mới có thể bị từ chối.',
                ]);
            }

            $locked->update([
                'status' => QuizVersionQuestionInvalidation::STATUS_REJECTED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            return $locked->fresh();
        });
    }

    /** @return Collection<int, QuizVersionQuestion> */
    public function activeMappings(QuizVersion $version): Collection
    {
        return $version->questionMappings()
            ->with('invalidations')
            ->whereHas('invalidations', fn ($query) => $query->active())
            ->get();
    }

    public function dispatchOrProcess(QuizVersionQuestionInvalidation $invalidation, int $completedAttempts): bool
    {
        if ($completedAttempts > QuizAttemptRegradeService::SYNC_ATTEMPT_LIMIT) {
            RegradeQuizQuestionJob::dispatch($invalidation->id);

            return true;
        }

        $this->processApproved($invalidation);

        return false;
    }

    private function assertPublishedHistoricalMapping(QuizVersionQuestion $mapping): void
    {
        abort_unless($mapping->quizVersion, 404);
        abort_unless(in_array($mapping->quizVersion->status, [
            QuizVersion::STATUS_PUBLISHED,
            QuizVersion::STATUS_SUPERSEDED,
        ], true), 422, 'Chỉ câu hỏi trong phiên bản đã xuất bản mới được hủy.');
        abort_unless(
            $mapping->questionVersion
                && (int) $mapping->questionVersion->question_id === (int) $mapping->question_id,
            422,
            'Ánh xạ câu hỏi của phiên bản Quiz không hợp lệ.',
        );
    }

    private function assertInstructorOwner(User $actor): void
    {
        abort_unless($actor->isInstructor() && ! $actor->isAdmin(), 403);
    }

    private function assertAdmin(User $actor): void
    {
        abort_unless($actor->isAdmin(), 403);
    }

    private function assertReason(string $reason): void
    {
        if (mb_strlen($reason) < 5 || mb_strlen($reason) > 5000) {
            throw ValidationException::withMessages([
                'reason' => 'Lý do phải có từ 5 đến 5.000 ký tự.',
            ]);
        }
    }
}
