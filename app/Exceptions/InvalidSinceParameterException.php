<?php

namespace App\Exceptions;

class InvalidSinceParameterException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
    }
}
