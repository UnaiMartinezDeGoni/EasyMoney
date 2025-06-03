<?php

namespace Tests\App\Http\Controllers\GetStreamerById;

use Tests\TestCase;
use App\Http\Controllers\GetStreamerById\GetStreamerByIdValidator;
use App\Exceptions\EmptyOrInvalidIdException;

class GetStreamerByIdValidatorTest extends TestCase
{
    private GetStreamerByIdValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GetStreamerByIdValidator();
    }

    public function validNumericIdIsReturned(): void
    {
        $this->assertSame('123', $this->validator->validate('123'));
        $this->assertSame('456', $this->validator->validate(' 456 '));
    }

    public function emptyIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('');
    }

    public function nullIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate(null);
    }

    public function nonNumericIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('abc');
    }

    public function zeroOrNegativeIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('0');

        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('-5');
    }
}
