<?php

namespace Tests\App\Http\Controllers\TopOfTheTops;

use App\Interfaces\DBRepositoriesInterface;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Services\AuthService;
use App\Interfaces\TwitchApiRepositoryInterface;
use Mockery;

class TopOfTheTopsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $mockDBRepo = Mockery::mock(DBRepositoriesInterface::class);
        $mockDBRepo->shouldReceive('isValidSession')
            ->andReturnUsing(function (string $token) {
                return $token === 'e59a7c4b2d301af8';
            });
        $this->app->instance(DBRepositoriesInterface::class, $mockDBRepo);


        $stubTwitchRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $this->app->instance(TwitchApiRepositoryInterface::class, $stubTwitchRepo);
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
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => "Bad Request. Invalid or missing parameters: 'since' must be a positive integer."
            ]);
    }

    /** @test */
    public function withInvalidTokenReturns401(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer invalidtoken']
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    /** @test */
    public function defaultSinceReturnsExpectedItems(): void
    {
        $mockDbRepo = Mockery::mock(DBRepositoriesInterface::class);
        $mockDbRepo->shouldReceive('isValidSession')
            ->andReturn(true);
        $mockDbRepo
            ->shouldReceive('getRecentTopVideos')
            ->once()
            ->andReturn([]);
        $mockDbRepo
            ->shouldReceive('existsTopGame')
            ->andReturn(true);
        $mockDbRepo
            ->shouldReceive('upsertTopVideo')
            ->andReturn(true);
        $this->app->instance(DBRepositoriesInterface::class, $mockDbRepo);


        $mockTwitchRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $mockTwitchRepo
            ->shouldReceive('getTopGames')
            ->withArgs(['dummy-token', 3])
            ->andReturn([
                [
                    'id'   => '509658',
                    'name' => 'Just Chatting',
                ],
            ]);
        $mockTwitchRepo
            ->shouldReceive('getVideosByGame')
            ->andReturn([]);
        $mockTwitchRepo
            ->shouldReceive('aggregateVideosByUser')
            ->andReturn([
                [
                    "game_id"                => "509658",
                    "game_name"              => "Just Chatting",
                    "user_name"              => "LCK",
                    "total_videos"           => "4",
                    "total_views"            => "1000000000",
                    "most_viewed_title"      => "DK vs T1 | 2021 LCK Summer\nFINALS",
                    "most_viewed_views"      => "5550000",
                    "most_viewed_duration"   => "5h52m8s",
                    "most_viewed_created_at" => "2015-02-20 16:47:56",
                ]
            ]);
        $this->app->instance(TwitchApiRepositoryInterface::class, $mockTwitchRepo);

        putenv('TWITCH_TOKEN=dummy-token');

        $response = $this->call(
            'GET',
            '/analytics/topsofthetops',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            '*' => [
                'game_id',
                'game_name',
                'user_name',
                'total_videos',
                'total_views',
                'most_viewed_title',
                'most_viewed_views',
                'most_viewed_duration',
                'most_viewed_created_at',
            ],
        ]);
        $response->assertJsonFragment([
            "game_id"                => "509658",
            "game_name"              => "Just Chatting",
            "user_name"              => "LCK",
            "total_videos"           => "4",
            "total_views"            => "1000000000",
            "most_viewed_title"      => "DK vs T1 | 2021 LCK Summer\nFINALS",
            "most_viewed_views"      => "5550000",
            "most_viewed_duration"   => "5h52m8s",
            "most_viewed_created_at" => "2015-02-20 16:47:56",
        ]);
    }
}
