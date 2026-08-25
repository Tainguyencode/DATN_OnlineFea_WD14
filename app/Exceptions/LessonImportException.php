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
    ) {
        parent::__construct($userMessage, 0, $previous);
    }
}
