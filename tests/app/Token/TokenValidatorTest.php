<?php

namespace Tests\app\Token;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Token\TokenValidator;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\EmptyApiKeyException;

class TokenValidatorTest extends TestCase
{
    protected TokenValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new TokenValidator();
    }

    /**
     */
    public function validDataDoesNotGetException(): void
    {
        $data = [
            'email'   => 'user@example.com',
            'api_key' => 'secret-key'
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    /**
     */
    public function getEmptyEmailExceptionWhenEmailIsMissing(): void
    {
        $this->expectException(EmptyEmailException::class);
        $data = [
            'email'   => '',
            'api_key' => 'some-api-key'
        ];
        $this->validator->validate($data);
    }

    /**
     */
    public function getInvalidEmailExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);
        $data = [
            'email'   => 'not-an-email',
            'api_key' => 'some-api-key'
        ];
        $this->validator->validate($data);
    }

    /**
     */
    public function getEmptyApiKeyExceptionWhenApiKeyIsMissing(): void
    {
        $this->expectException(EmptyApiKeyException::class);
        $data = [
            'email'   => 'user@example.com',
            'api_key' => ''
        ];
        $this->validator->validate($data);
    }
}
