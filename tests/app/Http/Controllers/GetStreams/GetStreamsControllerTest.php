<?php

namespace TwitchAnalytics\Tests\app\Http\Controllers\GetStreams;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use TwitchAnalytics\Tests\TestCase;
use App\Services\StreamAnalyticsService;

class GetStreamsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function withoutAuthHeaderReturns401(): void
    {
        $response = $this->call('GET', '/analytics/streams');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function withInvalidTokenReturns401(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer invalid']
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function validationErrorWhenLimitBelowMin(): void
    {
        $token = 'e59a7c4b2d301af8';
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=0',
            [], [], [], ['HTTP_AUTHORIZATION' => "Bearer {$token}"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    /**
     * @test
     */
    public function validationErrorWhenLimitAboveMax(): void
    {
        $token = 'e59a7c4b2d301af8';
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=101',
            [], [], [], ['HTTP_AUTHORIZATION' => "Bearer {$token}"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    /**
     * @test
     */
    public function validationErrorWhenLimitNotInteger(): void
    {
        $token = 'e59a7c4b2d301af8';
        $response = $this->call(
            'GET',
            '/analytics/streams?limit=foo',
            [], [], [], ['HTTP_AUTHORIZATION' => "Bearer {$token}"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    /**
     * @test
     */
    public function defaultLimitUsesTenAndReturnsData(): void
    {
        // Mockeamos el servicio para que devuelva 10 streams
        $mockData = array_map(fn($i) => ['id'=>"s{$i}", 'viewer_count'=>100+$i], range(1,10));
        $mockResponse = new JsonResponse($mockData, Response::HTTP_OK);

        $mockService = \Mockery::mock(StreamAnalyticsService::class);
        $mockService->shouldReceive('getStreams')
            ->once()
            ->with(10)
            ->andReturn($mockResponse);

        $this->app->instance(StreamAnalyticsService::class, $mockService);

        $response = $this->call(
            'GET',
            '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer valid']
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10)
            ->assertJson($mockData);
    }

    /**
     * @test
     */
    public function customLimitReturnsCorrectNumberOfItems(): void
    {
        $limit = 5;
        $mockData = array_map(fn($i) => ['id'=>"s{$i}", 'viewer_count'=>200+$i], range(1,$limit));
        $mockResponse = new JsonResponse($mockData, Response::HTTP_OK);

        $mockService = \Mockery::mock(StreamAnalyticsService::class);
        $mockService->shouldReceive('getStreams')
            ->once()
            ->with($limit)
            ->andReturn($mockResponse);

        $this->app->instance(StreamAnalyticsService::class, $mockService);

        $response = $this->call(
            'GET',
            "/analytics/streams?limit={$limit}",
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer valid']
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($limit)
            ->assertJson($mockData);
    }

    /**
     * @test
     */
    public function emptyResultReturnsEmptyArray(): void
    {
        $mockResponse = new JsonResponse([], Response::HTTP_OK);

        $mockService = \Mockery::mock(StreamAnalyticsService::class);
        $mockService->shouldReceive('getStreams')
            ->once()
            ->with(3)
            ->andReturn($mockResponse);

        $this->app->instance(StreamAnalyticsService::class, $mockService);

        $response = $this->call(
            'GET',
            '/analytics/streams?limit=3',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer valid']
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertExactJson([]);
    }
}
