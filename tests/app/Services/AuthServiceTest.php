<?php

namespace Tests\app\Services;

use App\Services\AuthService;
use App\Interfaces\DBRepositoriesInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $mockRepo;
    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepo = Mockery::mock(DBRepositoriesInterface::class);
        $this->service = new AuthService($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function validateTokenReturnsTrueWhenSessionIsValid(): void
    {
        $token = 'valid_token_123';

        $this->mockRepo
            ->shouldReceive('isValidSession')
            ->once()
            ->with($token)
            ->andReturnTrue();

        $result = $this->service->validateToken($token);

        $this->assertTrue($result);
    }

    /** @test */
    public function validateTokenReturnsFalseWhenSessionIsInvalid(): void
    {
        $token = 'invalid_token_456';

        $this->mockRepo
            ->shouldReceive('isValidSession')
            ->once()
            ->with($token)
            ->andReturnFalse();

        $result = $this->service->validateToken($token);

        $this->assertFalse($result);
    }


}
