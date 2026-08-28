<?php

namespace App\Support;

final class FullCourseImportWorkbookSchema
{
    public const VERSION = 3;

    public const SCHEMA = 'full_course_import';

    public const META_SHEET = '_meta';

    public const COURSE_SHEET = 'Course';

    public const SECTIONS_SHEET = 'Sections';

    public const LESSONS_SHEET = 'Lessons';

    public const QUIZZES_SHEET = 'Quizzes';

    public const QUIZ_QUESTIONS_SHEET = 'QuizQuestions';

    public const QUIZ_OPTIONS_SHEET = 'QuizOptions';

    public const SHEETS = [
        self::META_SHEET,
        self::COURSE_SHEET,
        self::SECTIONS_SHEET,
        self::LESSONS_SHEET,
        self::QUIZZES_SHEET,
        self::QUIZ_QUESTIONS_SHEET,
        self::QUIZ_OPTIONS_SHEET,
    ];

    public const COURSE_HEADERS = [
        'title', 'short_description', 'description', 'objectives', 'category_slug',
        'level', 'language', 'price', 'sale_price',
    ];

    public const SECTION_HEADERS = ['section_code', 'title', 'description'];

    public const LESSON_HEADERS = [
        'section_code', 'lesson_code', 'title', 'type', 'duration_seconds', 'content',
        'assignment_due_days', 'assignment_max_score', 'assignment_passing_score',
    ];

    public const MAX_SECTIONS = 50;

    public const MAX_LESSONS = 500;

    public const MAX_QUIZZES = 100;

    public const MAX_QUESTIONS = 2000;

    public const MAX_OPTIONS = 10000;
}
