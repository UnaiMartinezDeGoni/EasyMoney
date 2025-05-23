<?php

namespace Tests\app\Http\Controllers\TopOfTheTops;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\TopOfTheTops\TopOfTheTopsValidator;

class TopOfTheTopsValidatorTest extends TestCase
{
    public function testValidatorWithNoSinceParameter(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = [];  // No se define 'since'
        $this->expectNotToPerformAssertions();
        $validator->validate($data);
    }

    public function testValidatorWithValidSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => 500];
        $this->expectNotToPerformAssertions();
        $validator->validate($data);
    }

    public function testValidatorWithNonIntegerSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => 'invalid'];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
        $validator->validate($data);
    }

    public function testValidatorWithZeroSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => 0];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
        $validator->validate($data);
    }

    public function testValidatorWithNegativeSince(): void
    {
        $validator = new TopOfTheTopsValidator();
        $data = ['since' => -100];
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Bad Request. Invalid or missing parameters: 'since' must be a positive integer.");
        $validator->validate($data);
    }
}
