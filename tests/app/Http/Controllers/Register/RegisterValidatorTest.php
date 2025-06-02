<?php

namespace Tests\app\Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Http\Controllers\Register\RegisterValidator;
use Tests\TestCase;

class RegisterValidatorTest extends TestCase
{
    private RegisterValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new RegisterValidator();
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
