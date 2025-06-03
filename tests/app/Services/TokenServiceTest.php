<?php

namespace Tests\app\Services;

use App\Services\TokenService;
use App\Repositories\DB_Repositories;
use App\Exceptions\InvalidApiKeyException;
use Mockery;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    private $mockRepo;
    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepo = $this->getMockBuilder(DB_Repositories::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new TokenService($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function throwsInvalidApiKeyWhenUserNotFound(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('noone@example.com')
            ->willReturn(null);

        $this->expectException(InvalidApiKeyException::class);
        $this->expectExceptionMessage('API access token is invalid.');

        $this->service->issueToken('noone@example.com', 'any_key');
    }

    /**
     * @test
     */
    public function throwsInvalidApiKeyWhenApiKeyMismatch(): void
    {
        $user = ['id' => '10', 'api_key' => 'correct_key'];
        $this->mockRepo
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $this->expectException(InvalidApiKeyException::class);
        $this->expectExceptionMessage('API access token is invalid.');

        $this->service->issueToken('user@example.com', 'wrong_key');
    }

    /**
     * @test
     */
    public function returnsExistingTokenAndRefreshesSession(): void
    {
        $userId = 5;
        $user = ['id' => (string)$userId, 'api_key' => 'key123'];
        $existingToken = 'abcdef1234567890abcdef1234567890';

        $this->mockRepo
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $this->mockRepo
            ->expects($this->once())
            ->method('getActiveSession')
            ->with($userId)
            ->willReturn($existingToken);

        $this->mockRepo
            ->expects($this->once())
            ->method('refreshSession')
            ->with($userId, $existingToken)
            ->willReturn(true);

        $result = $this->service->issueToken('user@example.com', 'key123');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals($existingToken, $result['token']);
    }

    /**
     * @test
     */
    public function generatesNewTokenWhenNoActiveSession(): void
    {
        $userId = 8;
        $user = ['id' => (string)$userId, 'api_key' => 'keyABC'];

        $this->mockRepo
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('someone@example.com')
            ->willReturn($user);

        $this->mockRepo
            ->expects($this->once())
            ->method('getActiveSession')
            ->with($userId)
            ->willReturn(null);

        $this->mockRepo
            ->expects($this->once())
            ->method('createSession')
            ->with(
                $userId,
                $this->callback(fn($tok) =>
                    is_string($tok) && preg_match('/^[0-9a-f]{32}$/', $tok)
                ),
                $this->callback(fn($exp) =>
                    is_string($exp) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $exp)
                )
            )
            ->willReturn(true);

        $result = $this->service->issueToken('someone@example.com', 'keyABC');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $result['token']);
    }


}
