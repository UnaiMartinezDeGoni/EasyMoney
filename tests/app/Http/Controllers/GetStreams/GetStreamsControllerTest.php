<?php

namespace Tests\app\Http\Controllers\GetStreams;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use App\Services\GetStreamAnalyticsService;

class GetStreamsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock AuthService para no tocar la BD
        $mockAuth = \Mockery::mock(AuthService::class);
        $mockAuth->shouldReceive('validateToken')
            ->andReturnUsing(fn(string $token) => $token === 'e59a7c4b2d301af8');
        $this->app->instance(AuthService::class, $mockAuth);

        // Mock GetStreamAnalyticsService para devolver JSON según el limit
        $mockService = \Mockery::mock(GetStreamAnalyticsService::class);
        $mockService->shouldReceive('getStreams')
            ->andReturnUsing(fn(int $limit) => new JsonResponse(
                array_map(fn($i) => ['id' => "s{$i}", 'viewer_count' => 100 + $i], range(1, $limit)),
                Response::HTTP_OK
            ));
        $this->app->instance(GetStreamAnalyticsService::class, $mockService);
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

    public function testLimitBelowMinReturns422(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=0',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    public function testLimitAboveMaxReturns422(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=101',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    public function testLimitNotIntegerReturns422(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=foo',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    public function testDefaultLimitReturns10Items(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10)
            ->assertJsonFragment(['id' => 's1',  'viewer_count' => 101])
            ->assertJsonFragment(['id' => 's10', 'viewer_count' => 110]);
    }

    public function testCustomLimitReturnsCorrectNumberOfItems(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=5',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5)
            ->assertJsonFragment(['id' => 's5', 'viewer_count' => 105]);
    }

    public function testEmptyResultReturnsEmptyArray(): void
    {
        // Rebind para devolver siempre []
        $this->app->instance(GetStreamAnalyticsService::class, \Mockery::mock(GetStreamAnalyticsService::class, function ($m) {
            $m->shouldReceive('getStreams')->andReturn(new JsonResponse([], Response::HTTP_OK));
        }));

        $response = $this->call(
            'GET',
            '/analytics/streams?limit=3',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_OK)
            ->assertExactJson([]);
    }
}
