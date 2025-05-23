<?php

namespace App\Http\Controllers\Token;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\EmptyApiKeyException;

class TokenValidator
{
    public function validate(array $data): void
    {
        if (empty($data['email'])) {
            throw new EmptyEmailException('The email is mandatory.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException('The email must be a valid email address.');
        }

        if (empty($data['api_key'])) {
            throw new EmptyApiKeyException('The api_key is mandatory.');
        }
    }
}
