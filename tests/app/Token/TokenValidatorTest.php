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
     * Verifica que no se lance excepción cuando se envían datos válidos.
     */
    public function testValidDataDoesNotThrowException(): void
    {
        $data = [
            'email'   => 'user@example.com',
            'api_key' => 'secret-key'
        ];

        // Si la validación no lanza excepción, la prueba es satisfactoria.
        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    /**
     * Verifica que se lance la excepción EmptyEmailException cuando no se proporciona el email.
     */
    public function testThrowsEmptyEmailExceptionWhenEmailIsMissing(): void
    {
        $this->expectException(EmptyEmailException::class);
        $data = [
            'email'   => '',
            'api_key' => 'some-api-key'
        ];
        $this->validator->validate($data);
    }

    /**
     * Verifica que se lance la excepción InvalidEmailException cuando se proporciona un email inválido.
     */
    public function testThrowsInvalidEmailExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);
        $data = [
            'email'   => 'not-an-email',
            'api_key' => 'some-api-key'
        ];
        $this->validator->validate($data);
    }

    /**
     * Verifica que se lance la excepción EmptyApiKeyException cuando falta el api_key.
     */
    public function testThrowsEmptyApiKeyExceptionWhenApiKeyIsMissing(): void
    {
        $this->expectException(EmptyApiKeyException::class);
        $data = [
            'email'   => 'user@example.com',
            'api_key' => ''
        ];
        $this->validator->validate($data);
    }
}