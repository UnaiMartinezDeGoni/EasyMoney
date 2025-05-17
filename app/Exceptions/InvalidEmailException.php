<?php

namespace App\Exceptions;

class InvalidEmailException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The email must be a valid email address');
    }
}
