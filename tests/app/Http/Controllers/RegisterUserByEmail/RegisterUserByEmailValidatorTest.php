<?php

namespace Tests\app\Http\Controllers\RegisterUserByEmail;

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
    /**
     * @test
     */
    public function getsEmptyEmailException(): void
    {
        $this->expectException(EmptyEmailException::class);
        $this->validator->validate(['email' => '']);
    }
    /**
     * @test
     */
    public function getsInvalidEmailException(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->validator->validate(['email' => 'invalid-email']);
    }
}
