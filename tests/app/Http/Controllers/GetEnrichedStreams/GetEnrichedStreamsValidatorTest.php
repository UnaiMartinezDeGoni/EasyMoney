<?php

namespace Tests\app\Http\Controllers\GetEnrichedStreams;


use App\Exceptions\InvalidLimitException;
use App\Http\Controllers\GetEnrichedStreams\GetEnrichedStreamsValidator;
use PHPUnit\Framework\TestCase;

class GetEnrichedStreamsValidatorTest extends TestCase
{
    private GetEnrichedStreamsValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GetEnrichedStreamsValidator();
    }

    /** @test */
    public function givenMissingLimitThrowsException(): void
    {
        $this->expectException(InvalidLimitException::class);
        $this->validator->validate(null);
    }

    /** @test */
    public function givenInvalidLimitThrowsException(): void
    {
        $this->expectException(InvalidLimitException::class);
        $this->validator->validate('not_number');
    }

    /** @test */
    public function givenNegativeLimitThrowsException(): void
    {
        $this->expectException(InvalidLimitException::class);
        $this->validator->validate('-5');
    }

    /** @test */
    public function givenLimitGreaterThan20ThrowsException(): void
    {
        $this->expectException(InvalidLimitException::class);
        $this->validator->validate('25');
    }

    /** @test */
    public function givenValidLimitReturnsSanitizedValue(): void
    {
        $result = $this->validator->validate('10');
        $this->assertSame('10', $result);
    }
}
