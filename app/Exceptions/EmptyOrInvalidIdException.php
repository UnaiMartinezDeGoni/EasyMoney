<?php
// app/Exceptions/EmptyOrInvalidIdException.php

namespace App\Exceptions;

use InvalidArgumentException;

class EmptyOrInvalidIdException extends InvalidArgumentException
{
    public function __construct(
        string $message = "Invalid or missing 'id' parameter.",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
