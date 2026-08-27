<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_version_question_invalidations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('quiz_version_question_id')->unique('qvqi_invalidation_mapping_unique');
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('invalidated_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('invalidated_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reason');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('regrade_started_at')->nullable();
            $table->timestamp('regrade_completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'qvqi_invalidation_status_created_idx');
            $table->foreign('quiz_version_question_id', 'qvqi_invalidation_mapping_fk')
                ->references('id')->on('quiz_version_questions')->restrictOnDelete();
            $table->foreign('requested_by', 'qvqi_invalidation_requested_by_fk')
                ->references('id')->on('users')->restrictOnDelete();
            $table->foreign('invalidated_by', 'qvqi_invalidation_invalidated_by_fk')
                ->references('id')->on('users')->restrictOnDelete();
            $table->foreign('reviewed_by', 'qvqi_invalidation_reviewed_by_fk')
                ->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('quiz_attempt_regrades', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('quiz_attempt_id');
            $table->unsignedBigInteger('invalidation_id');
            $table->unsignedInteger('original_score');
            $table->unsignedInteger('original_total_score');
            $table->decimal('original_percent', 5, 2);
            $table->boolean('original_passed');
            $table->unsignedInteger('recalculated_score');
            $table->unsignedInteger('recalculated_total_score');
            $table->decimal('recalculated_percent', 5, 2);
            $table->boolean('recalculated_passed');
            $table->unsignedInteger('effective_score');
            $table->unsignedInteger('effective_total_score');
            $table->decimal('effective_percent', 5, 2);
            $table->boolean('effective_passed');
            $table->timestamp('regraded_at');
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'invalidation_id'], 'quiz_attempt_regrades_attempt_invalidation_unique');
            $table->index(['invalidation_id', 'regraded_at'], 'quiz_attempt_regrades_invalidation_regraded_idx');
            $table->foreign('quiz_attempt_id', 'quiz_attempt_regrades_attempt_fk')
                ->references('id')->on('quiz_attempts')->restrictOnDelete();
            $table->foreign('invalidation_id', 'quiz_attempt_regrades_invalidation_fk')
                ->references('id')->on('quiz_version_question_invalidations')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_regrades');
        Schema::dropIfExists('quiz_version_question_invalidations');
    }
};
