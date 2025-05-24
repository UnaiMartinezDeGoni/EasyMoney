<?php

namespace Tests\app\Http\Controllers\GetStreams;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use App\Services\GetLiveStreamsService;

class GetStreamsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock AuthService para validar tokens
        $mockAuth = \Mockery::mock(AuthService::class);
        $mockAuth->shouldReceive('validateToken')
            ->andReturnUsing(fn(string $token) => $token === 'e59a7c4b2d301af8');
        $this->app->instance(AuthService::class, $mockAuth);

        // Mock GetLiveStreamsService para devolver un listado fijo
        $mockService = \Mockery::mock(GetLiveStreamsService::class);
        $mockService->shouldReceive('getStreams')
            ->andReturn(new JsonResponse([
                ['title' => 'Stream 1', 'user_name' => 'User1'],
                ['title' => 'Stream 2', 'user_name' => 'User2'],
            ], Response::HTTP_OK));
        $this->app->instance(GetLiveStreamsService::class, $mockService);
    }

    public function testWithoutAuthHeaderReturns401(): void
    {
        $response = $this->call('GET', '/analytics/streams');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testWithInvalidTokenReturns401(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer invalid_token']
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testValidRequestReturnsExpectedData(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['title' => 'Stream 1', 'user_name' => 'User1'])
            ->assertJsonFragment(['title' => 'Stream 2', 'user_name' => 'User2']);
    }

    public function testEmptyResultReturnsEmptyArray(): void
    {
        // Rebind el servicio para devolver []
        $this->app->instance(GetLiveStreamsService::class, \Mockery::mock(GetLiveStreamsService::class, function ($m) {
            $m->shouldReceive('getStreams')->andReturn(new JsonResponse([], Response::HTTP_OK));
        }));

        $response = $this->call(
            'GET',
            '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK)->assertExactJson([]);
    }
}
