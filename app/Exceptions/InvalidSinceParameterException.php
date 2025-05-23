<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidSinceParameterException extends RuntimeException
{
    public function __construct(
        string $message = "Bad Request. Invalid or missing parameters: 'since' must be a positive integer.",
        int $code = 400,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
