<?php

namespace App\Exceptions;

use Exception;

class LessonAiException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $codeKey = 'ai_error',
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }
}
