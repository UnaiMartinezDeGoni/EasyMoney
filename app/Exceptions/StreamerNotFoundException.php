<?php

namespace App\Exceptions;

use RuntimeException;

class StreamerNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User not found.');
    }
}
