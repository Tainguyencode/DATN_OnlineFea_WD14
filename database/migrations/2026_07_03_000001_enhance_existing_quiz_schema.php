<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('quizzes')) {
            if (! Schema::hasColumn('quizzes', 'description')) {
                Schema::table('quizzes', function (Blueprint $table) {
                    $table->text('description')->nullable()->after('title');
                });
            }

            if (! Schema::hasColumn('quizzes', 'max_attempts')) {
                Schema::table('quizzes', function (Blueprint $table) {
                    $table->unsignedInteger('max_attempts')->nullable()->after('time_limit_minutes');
                });
            }

            if (! Schema::hasColumn('quizzes', 'is_active')) {
                Schema::table('quizzes', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('max_attempts');
                });
            }
        }

        if (Schema::hasTable('quiz_questions')) {
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE quiz_questions MODIFY type ENUM('single', 'multiple', 'true_false') NOT NULL DEFAULT 'single'");
            }

            if (! Schema::hasColumn('quiz_questions', 'explanation')) {
                Schema::table('quiz_questions', function (Blueprint $table) {
                    $table->text('explanation')->nullable()->after('points');
                });
            }
        }

        if (Schema::hasTable('quiz_options') && ! Schema::hasColumn('quiz_options', 'sort_order')) {
            Schema::table('quiz_options', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_correct');
            });
        }

        if (Schema::hasTable('quiz_attempts')) {
            if (! Schema::hasColumn('quiz_attempts', 'total_score')) {
                Schema::table('quiz_attempts', function (Blueprint $table) {
                    $table->unsignedInteger('total_score')->default(0)->after('score');
                });
            }

            if (! Schema::hasColumn('quiz_attempts', 'percent')) {
                Schema::table('quiz_attempts', function (Blueprint $table) {
                    $table->decimal('percent', 5, 2)->default(0)->after('total_score');
                });
            }
        }

        if (! Schema::hasTable('quiz_attempt_answers')) {
            Schema::create('quiz_attempt_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_attempt_id')
                    ->constrained('quiz_attempts')
                    ->cascadeOnDelete();
                $table->foreignId('question_id')
                    ->constrained('quiz_questions')
                    ->cascadeOnDelete();
                $table->foreignId('answer_id')
                    ->nullable()
                    ->constrained('quiz_options')
                    ->nullOnDelete();
                $table->boolean('is_correct')->default(false);
                $table->timestamps();

                $table->index(['quiz_attempt_id', 'question_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('quiz_attempt_answers')) {
            Schema::dropIfExists('quiz_attempt_answers');
        }

        if (Schema::hasTable('quiz_attempts')) {
            $columns = array_values(array_filter([
                Schema::hasColumn('quiz_attempts', 'percent') ? 'percent' : null,
                Schema::hasColumn('quiz_attempts', 'total_score') ? 'total_score' : null,
            ]));

            if ($columns !== []) {
                Schema::table('quiz_attempts', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }

        if (Schema::hasTable('quiz_options') && Schema::hasColumn('quiz_options', 'sort_order')) {
            Schema::table('quiz_options', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasTable('quiz_questions') && Schema::hasColumn('quiz_questions', 'explanation')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->dropColumn('explanation');
            });
        }

        if (Schema::hasTable('quizzes')) {
            $columns = array_values(array_filter([
                Schema::hasColumn('quizzes', 'is_active') ? 'is_active' : null,
                Schema::hasColumn('quizzes', 'max_attempts') ? 'max_attempts' : null,
                Schema::hasColumn('quizzes', 'description') ? 'description' : null,
            ]));

            if ($columns !== []) {
                Schema::table('quizzes', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }
};
