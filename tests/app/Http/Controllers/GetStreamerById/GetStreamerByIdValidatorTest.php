<?php

namespace tests\App\Http\Controllers\GetStreamerById;

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
    /**
     * @test
     */
    public function validNumericIdIsReturned(): void
    {
        $this->assertSame('123', $this->validator->validate('123'));
        $this->assertSame('456', $this->validator->validate(' 456 '));
    }
    /**
     * @test
     */
    public function emptyIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('');
    }
    /**
     * @test
     */
    public function nullIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate(null);
    }
    /**
     * @test
     */
    public function nonNumericIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('abc');
    }
    /**
     * @test
     */
    public function zeroOrNegativeIdThrowsException(): void
    {
        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('0');

        $this->expectException(EmptyOrInvalidIdException::class);
        $this->validator->validate('-5');
    }
}
