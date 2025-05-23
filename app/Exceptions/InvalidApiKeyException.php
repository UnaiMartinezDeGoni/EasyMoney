<?php

namespace App\Exceptions;

class InvalidApiKeyException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('API access token is invalid.');
    }
}
