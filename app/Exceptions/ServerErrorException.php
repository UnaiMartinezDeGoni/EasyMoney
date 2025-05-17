<?php

namespace App\Exceptions;

class ServerErrorException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Internal server error.');
    }
}
