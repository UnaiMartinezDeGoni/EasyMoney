<?php

namespace Tests\App\Http\Controllers\GetEnrichedStreams;

use App\Interfaces\DBRepositoriesInterface;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Services\AuthService;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetEnrichedStreamsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $mockDBRepo = \Mockery::mock(DBRepositoriesInterface::class);
        $mockDBRepo->shouldReceive('isValidSession')
            ->andReturnUsing(function (string $token) {
                return $token === 'e59a7c4b2d301af8';
            });
        $this->app->instance(DBRepositoriesInterface::class, $mockDBRepo);


        $mockRepo = \Mockery::mock(TwitchApiRepositoryInterface::class);

        $mockRepo->shouldReceive('getStreams')
            ->andReturn([
                [
                    'id'           => 's1',
                    'user_id'      => 'u1',
                    'user_name'    => 'user1',
                    'viewer_count' => 50,
                    'title'        => 'title1',
                ],
                [
                    'id'           => 's2',
                    'user_id'      => 'u2',
                    'user_name'    => 'user2',
                    'viewer_count' => 100,
                    'title'        => 'title2',
                ],
            ]);

        $mockRepo->shouldReceive('getStreamerById')
            ->andReturnUsing(function (string $userId) {
                return match ($userId) {
                    'u2' => [
                        'display_name'      => 'UserTwoDisplay',
                        'profile_image_url' => 'https://example.com/u2.png',
                    ],
                    'u1' => [
                        'display_name'      => 'UserOneDisplay',
                        'profile_image_url' => 'https://example.com/u1.png',
                    ],
                    default => []
                };
            });

        $this->app->instance(TwitchApiRepositoryInterface::class, $mockRepo);
    }
    /** @test */
    public function withInvalidTokenReturns401(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams/enriched?limit=1',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer invalidtoken']
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    /** @test */
    public function get400WhenMissingLimit(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams/enriched',
            [], // sin 'limit'
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'error' => "Invalid 'limit' parameter.",
        ]);
    }

    /** @test */
    public function get400WhenInvalidLimit(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams/enriched',
            ['limit' => 'not_a_number'],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'error' => "Invalid 'limit' parameter.",
        ]);
    }

    /** @test */
    public function get200WhenValidLimit(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams/enriched',
            ['limit' => '1'],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            '*' => [
                'stream_id',
                'user_id',
                'user_name',
                'viewer_count',
                'title',
                'user_display_name',
                'profile_image_url',
            ],
        ]);

        $response->assertJsonFragment([
            'stream_id'         => 's2',
            'user_id'           => 'u2',
            'user_name'         => 'user2',
            'viewer_count'      => 100,
            'title'             => 'title2',
            'user_display_name' => 'UserTwoDisplay',
            'profile_image_url' => 'https://example.com/u2.png',
        ]);
    }
}
