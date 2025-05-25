<?php
// app/Exceptions/TwitchUnauthorizedException.php

namespace App\Exceptions;

use Exception;

class TwitchUnauthorizedException extends Exception
{
    public function __construct(
        string $message = 'Twitch access token is invalid or has expired.',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
