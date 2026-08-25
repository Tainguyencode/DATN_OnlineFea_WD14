<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class LessonImportException extends RuntimeException
{
    public function __construct(
        public readonly string $issueCode,
        public readonly string $userMessage,
        ?Throwable $previous = null,
        public readonly int $httpStatus = 422,
    ) {
        parent::__construct($userMessage, 0, $previous);
    }
}
