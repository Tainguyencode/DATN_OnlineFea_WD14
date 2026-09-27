<?php

namespace Tests\Unit;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Tests\TestCase;

class QuizProctoringDomainTest extends TestCase
{
    public function test_terminated_attempt_counts_towards_completed_attempts_count(): void
    {
        $quiz = new Quiz();
        $quiz->max_attempts = 3;

        $attempt = new QuizAttempt([
            'status' => QuizAttempt::STATUS_TERMINATED,
            'termination_reason' => QuizAttempt::REASON_TAB_SWITCH,
        ]);

        $this->assertTrue($attempt->isTerminated());
        $this->assertTrue($attempt->isFinalized());
        $this->assertSame('Chuyển sang tab khác trong khi làm bài', $attempt->getTerminationReasonLabel());
    }

    public function test_proctoring_constants_and_reasons(): void
    {
        $this->assertSame('tab_switch', QuizAttempt::REASON_TAB_SWITCH);
        $this->assertSame('window_blur', QuizAttempt::REASON_WINDOW_BLUR);
        $this->assertSame('fullscreen_exit', QuizAttempt::REASON_FULLSCREEN_EXIT);
        $this->assertSame('page_exit', QuizAttempt::REASON_PAGE_EXIT);
        $this->assertSame('time_expired', QuizAttempt::REASON_TIME_EXPIRED);
        $this->assertSame('submitted', QuizAttempt::REASON_SUBMITTED);

        $this->assertSame('in_progress', QuizAttempt::STATUS_IN_PROGRESS);
        $this->assertSame('completed', QuizAttempt::STATUS_COMPLETED);
        $this->assertSame('terminated', QuizAttempt::STATUS_TERMINATED);
        $this->assertSame('expired', QuizAttempt::STATUS_EXPIRED);
    }
}
