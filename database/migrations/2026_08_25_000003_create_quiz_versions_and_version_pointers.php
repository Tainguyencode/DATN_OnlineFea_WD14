<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CURRENT_DRAFT_FOREIGN = 'quizzes_current_draft_version_foreign';

    private const CURRENT_PUBLISHED_FOREIGN = 'quizzes_current_published_version_foreign';

    public function up(): void
    {
        Schema::create('quiz_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quiz_id')
                ->constrained('quizzes')
                ->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('pass_score')->default(70);
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('max_attempts')->nullable();
            $table->enum('status', ['draft', 'published', 'superseded']);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['quiz_id', 'version']);
            $table->index(['quiz_id', 'status']);
        });

        Schema::table('quizzes', function (Blueprint $table): void {
            $table->unsignedBigInteger('current_published_version_id')
                ->nullable()
                ->after('is_active');
            $table->unsignedBigInteger('current_draft_version_id')
                ->nullable()
                ->after('current_published_version_id');

            $table->foreign('current_published_version_id', self::CURRENT_PUBLISHED_FOREIGN)
                ->references('id')
                ->on('quiz_versions')
                ->nullOnDelete();
            $table->foreign('current_draft_version_id', self::CURRENT_DRAFT_FOREIGN)
                ->references('id')
                ->on('quiz_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('quiz_versions')->exists()) {
            throw new RuntimeException(
                'Quiz versioning rollback stopped because quiz_versions contains historical data.',
            );
        }

        $driver = DB::connection()->getDriverName();

        Schema::table('quizzes', function (Blueprint $table) use ($driver): void {
            if ($driver === 'sqlite') {
                $table->dropForeign(['current_draft_version_id']);
                $table->dropForeign(['current_published_version_id']);
            } else {
                $table->dropForeign(self::CURRENT_DRAFT_FOREIGN);
                $table->dropForeign(self::CURRENT_PUBLISHED_FOREIGN);
            }

            $table->dropColumn(['current_draft_version_id', 'current_published_version_id']);
        });

        Schema::dropIfExists('quiz_versions');
    }
};
