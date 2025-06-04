<?php

namespace Tests\App\Http\Controllers\TopOfTheTops;

use Tests\TestCase;
use Illuminate\Http\Response;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Services\TopOfTheTopsService;
use App\Services\AuthService;
use Mockery;

class TopOfTheTopsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        $mockAuth = Mockery::mock(AuthService::class);
        $mockAuth
            ->shouldReceive('validateToken')
            ->andReturnUsing(fn (string $token) => $token === 'e59a7c4b2d301af8');
        $this->app->instance(AuthService::class, $mockAuth);

        $mockRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $mockRepo
            ->shouldReceive('getTopVideos')
            ->andReturnUsing(function (int $since) {
                if ($since === 600) {
                    return array_map(
                        fn ($i) => ['id' => "v{$i}", 'views' => 100 + $i],
                        range(1, 5)
                    );
                } elseif ($since === 120) {
                    return array_map(
                        fn ($i) => ['id' => "v{$i}", 'views' => 200 + $i],
                        range(1, 2)
                    );
                }
                return [];
            });
        $this->app->instance(TwitchApiRepositoryInterface::class, $mockRepo);

        $service = new TopOfTheTopsService($mockRepo);
        $this->app->instance(TopOfTheTopsService::class, $service);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function invalidSinceParameterReturns400(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops?since=abc',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
                 ->assertJson([
                     'error' => "Bad Request. Invalid or missing parameters: 'since' must be a positive integer."
                 ]);
    }

    /** @test */
    public function defaultSinceReturnsExpectedItems(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(5)
                 ->assertJsonFragment(['id' => 'v1', 'views' => 101]);
    }

    /** @test */
    public function customSinceParameterReturnsExpectedItems(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops?since=120',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['id' => 'v1', 'views' => 201]);
    }

    /** @test */
    public function emptyResultReturns404(): void
    {
        $mockRepoEmpty = Mockery::mock(TwitchApiRepositoryInterface::class);
        $mockRepoEmpty
            ->shouldReceive('getTopVideos')
            ->andReturn([]);
        $this->app->instance(TwitchApiRepositoryInterface::class, $mockRepoEmpty);

        $serviceEmpty = new TopOfTheTopsService($mockRepoEmpty);
        $this->app->instance(TopOfTheTopsService::class, $serviceEmpty);

        $response = $this->call(
            'GET',
            '/analytics/topsofthetops?since=120',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND)
                 ->assertExactJson(['error' => 'Not Found. No data available.']);
    }
}
