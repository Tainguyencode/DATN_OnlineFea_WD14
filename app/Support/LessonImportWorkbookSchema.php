<?php

namespace App\Support;

final class LessonImportWorkbookSchema
{
    public const SCHEMA = 'lesson_import';

    public const VERSION_V1 = 1;

    public const VERSION_V2 = 2;

    public const META_SHEET = '_meta';

    public const LESSONS_SHEET = 'Lessons';

    public const QUIZZES_SHEET = 'Quizzes';

    public const QUIZ_QUESTIONS_SHEET = 'QuizQuestions';

    public const QUIZ_OPTIONS_SHEET = 'QuizOptions';

    public const V1_SHEETS = [
        self::META_SHEET,
        self::LESSONS_SHEET,
    ];

    public const V2_SHEETS = [
        self::META_SHEET,
        self::LESSONS_SHEET,
        self::QUIZZES_SHEET,
        self::QUIZ_QUESTIONS_SHEET,
        self::QUIZ_OPTIONS_SHEET,
    ];

    public const V1_LESSON_HEADERS = [
        'lesson_code',
        'title',
        'type',
        'duration_seconds',
        'content',
        'assignment_due_days',
        'assignment_max_score',
        'assignment_passing_score',
    ];

    public const V2_LESSON_HEADERS = self::V1_LESSON_HEADERS;

    public const QUIZ_HEADERS = [
        'lesson_code',
        'title',
        'description',
        'pass_score',
        'time_limit_minutes',
        'max_attempts',
        'is_active',
    ];

    public const QUIZ_QUESTION_HEADERS = [
        'lesson_code',
        'question_code',
        'question',
        'type',
        'points',
        'explanation',
    ];

    public const QUIZ_OPTION_HEADERS = [
        'question_code',
        'option_code',
        'option_text',
        'is_correct',
    ];

    public const QUESTION_TYPES = [
        'single',
        'multiple',
        'true_false',
    ];

    public const BOOLEAN_TEMPLATE_VALUES = [
        'TRUE',
        'FALSE',
    ];
}
