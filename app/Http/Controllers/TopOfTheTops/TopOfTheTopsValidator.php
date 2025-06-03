<?php

namespace App\Http\Controllers\TopOfTheTops;

use App\Exceptions\InvalidSinceParameterException;

class TopOfTheTopsValidator
{
    public function validate(array $data): void
    {
        if (isset($data['since'])) {
            if (!filter_var($data['since'], FILTER_VALIDATE_INT) || (int)$data['since'] <= 0) {
                throw new InvalidSinceParameterException();
            }
        }
    }
}
