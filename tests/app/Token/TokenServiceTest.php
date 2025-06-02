<?php

namespace Tests\Unit\Services;

use App\Services\TokenService;
use App\Repositories\DB_Repositories;
use App\Exceptions\InvalidApiKeyException;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    private DB_Repositories $mockRepo;
    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepo = $this->getMockBuilder(DB_Repositories::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $this->service = new TokenService($this->mockRepo);
    }

    /**
     * @test
     */
    public function getsExceptionWhenUserNotFound(): void
    {
        $this->mockRepo->expects($this->once())
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
    public function getExceptionWhenApiKeyMismatch(): void
    {
        $user = ['id' => '10', 'api_key' => 'correct_key'];
        $this->mockRepo->method('findUserByEmail')
                       ->willReturn($user);

        $this->expectException(InvalidApiKeyException::class);

        $this->service->issueToken('user@example.com', 'wrong_key');
    }

    /**
     * @test
     */
    public function getExistingTokenAndRefreshesSession(): void
    {
        $userId = 5;
        $user = ['id' => (string)$userId, 'api_key' => 'key123'];
        $existingToken = 'abcdef1234567890abcdef1234567890';

        $this->mockRepo->method('findUserByEmail')
                       ->willReturn($user);
        $this->mockRepo->method('getActiveSession')
                       ->with($userId)
                       ->willReturn($existingToken);
        $this->mockRepo->expects($this->once())
                       ->method('refreshSession')
                       ->with($userId, $existingToken);

        $result = $this->service->issueToken('user@example.com', 'key123');
        $this->assertSame($existingToken, $result);
    }

    /**
     * @test
     */
    public function getNewTokenWhenNoActiveSession(): void
    {
        $userId = 8;
        $user = ['id' => (string)$userId, 'api_key' => 'keyABC'];

        $this->mockRepo->method('findUserByEmail')
                       ->willReturn($user);
        $this->mockRepo->method('getActiveSession')
                       ->with($userId)
                       ->willReturn(null);

        // Esperamos llamada a createSession
        $this->mockRepo->expects($this->once())
                       ->method('createSession')
                       ->with(
                           $userId,
                           $this->callback(fn($tok) => is_string($tok) && preg_match('/^[0-9a-f]{32}$/', $tok)),
                           $this->callback(fn($exp) => is_string($exp) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $exp))
                       );

        $newToken = $this->service->issueToken('someone@example.com', 'keyABC');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $newToken);
    }
}
