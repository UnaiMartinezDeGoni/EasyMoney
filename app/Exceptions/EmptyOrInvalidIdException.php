<?php
// app/Exceptions/EmptyOrInvalidIdException.php

namespace App\Exceptions;

use InvalidArgumentException;

class EmptyOrInvalidIdException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Invalid or missing 'id' parameter.");
    }
}
