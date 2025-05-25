<?php
// tests/Feature/GetStreamerByIdControllerTest.php

namespace Tests\App\Http\Controllers\GetStreamerById;

use Tests\TestCase;
use Mockery;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class GetStreamerByIdControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testReturns401IfNoTokenProvided(): void
    {
        $this->json('GET', '/analytics/streamer?id=1')
            ->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized']);
    }

    public function testReturns400IfMissingIdParam(): void
    {
        $this->app->instance(AuthService::class, new class {
            public function validateToken(string $token): bool { return true; }
        });

        $this->json('GET', '/analytics/streamer', [], ['Authorization'=>'Bearer x'])
            ->seeStatusCode(400)
            ->seeJsonEquals(['error'=>"Invalid or missing 'id' parameter."]);
    }

    public function testReturns400IfInvalidIdProvided(): void
    {
        $this->app->instance(AuthService::class, new class {
            public function validateToken(string $token): bool { return true; }
        });

        $this->json('GET', '/analytics/streamer?id=abc', [], ['Authorization'=>'Bearer x'])
            ->seeStatusCode(400)
            ->seeJsonEquals(['error'=>"Invalid or missing 'id' parameter."]);
    }

    public function testReturns200WhenFound(): void
    {
        $this->app->instance(AuthService::class, new class {
            public function validateToken(string $token): bool { return true; }
        });

        $repo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repo->shouldReceive('getStreamerById')
            ->once()
            ->with('1')
            ->andReturn(['id'=>'1','login'=>'streamer1']);
        $this->app->instance(TwitchApiRepositoryInterface::class, $repo);

        $this->json('GET','/analytics/streamer?id=1',[],['Authorization'=>'Bearer x'])
            ->seeStatusCode(200)
            ->seeJson(['id'=>'1','login'=>'streamer1']);
    }

    public function testReturns404IfNotFound(): void
    {
        $this->app->instance(AuthService::class, new class {
            public function validateToken(string $token): bool { return true; }
        });

        $repo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repo->shouldReceive('getStreamerById')
            ->once()
            ->with('999')
            ->andReturn([]);
        $this->app->instance(TwitchApiRepositoryInterface::class, $repo);

        $this->json('GET','/analytics/streamer?id=999',[],['Authorization'=>'Bearer x'])
            ->seeStatusCode(404)
            ->seeJsonEquals(['error'=>'Streamer not found.']);
    }
}
