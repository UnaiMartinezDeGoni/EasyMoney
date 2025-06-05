<?php

namespace App\Exceptions;

class TwitchUnauthorizedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized. Twitch access token is invalid or has expired.');
    }
}
