<?php

namespace App\Http\Controllers\RegisterUserByEmail;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;

class RegisterUserByEmailValidator
{
    public function validate(array $data): void
    {
        if (empty($data['email'])) {
            throw new EmptyEmailException();
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }

}
