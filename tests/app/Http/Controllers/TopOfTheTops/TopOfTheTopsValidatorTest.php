<?php

namespace Tests\app\Http\Controllers\TopOfTheTops;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\TopOfTheTops\TopOfTheTopsValidator;

class TopOfTheTopsValidatorTest extends TestCase
{
    /** @test */
    public function validatorWithNoSinceParameter(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = [];  // No se define 'since'
        $this->expectNotToPerformAssertions();
        $validator->validate($data);
    }

    /** @test */
    public function validatorWithValidSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => 500];
        $this->expectNotToPerformAssertions();
        $validator->validate($data);
    }

    /** @test */
    public function validatorWithNonIntegerSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => 'invalid'];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
        $validator->validate($data);
    }

    /** @test */
    public function validatorWithZeroSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => 0];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
        $validator->validate($data);
    }

    /** @test */
    public function validatorWithNegativeSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => -100];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
        $validator->validate($data);
    }
}
