<?php

namespace Tests\app\RegisterUserByEmail;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailValidator;
use Tests\TestCase;

class RegisterUserByEmailValidatorTest extends TestCase
{
    private RegisterUserByEmailValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new RegisterUserByEmailValidator();
    }

    public function testEmptyEmailThrowsEmptyEmailException(): void
    {
        $this->expectException(EmptyEmailException::class);
        $this->validator->validate(['email' => '']);
    }

    public function testInvalidEmailThrowsInvalidEmailException(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->validator->validate(['email' => 'invalid-email']);
    }
}
