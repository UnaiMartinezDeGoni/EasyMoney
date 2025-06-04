<?php

namespace Tests\app\Services;

use App\Services\TokenService;
use App\Repositories\DBRepositories;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    private $mockRepo;
    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepo = $this->getMockBuilder(DBRepositories::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new TokenService($this->mockRepo);
    }

    /**
     * @test
     */
    public function gets401WhenUserNotFound(): void
    {
        $this->mockRepo
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('noone@example.com')
            ->willReturn(null);

        $response = $this->service->issueToken('noone@example.com', 'any_key');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        $expected = json_encode(
            ['error' => 'Unauthorized. API access token is invalid.'],
            JSON_PRETTY_PRINT
        );
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    /**
     * @test
     */
    public function gets401WhenApiKeyMismatch(): void
    {
        $user = ['id' => '10', 'api_key' => 'correct_key'];
        $this->mockRepo
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $response = $this->service->issueToken('user@example.com', 'wrong_key');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        $expected = json_encode(
            ['error' => 'Unauthorized. API access token is invalid.'],
            JSON_PRETTY_PRINT
        );
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    /**
     * @test
     */
    public function getsExistingTokenAndRefreshesSession(): void
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

        $response = $this->service->issueToken('user@example.com', 'key123');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $expected = json_encode(
            ['token' => $existingToken],
            JSON_PRETTY_PRINT
        );
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    /**
     * @test
     */
    public function getsNewTokenWhenNoActiveSession(): void
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

        $response = $this->service->issueToken('someone@example.com', 'keyABC');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $content);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $content['token']);
    }
}
