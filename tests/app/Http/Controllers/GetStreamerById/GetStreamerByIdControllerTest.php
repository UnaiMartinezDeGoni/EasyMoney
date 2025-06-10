<?php

namespace Tests\App\Http\Controllers\GetStreamerById;

use App\Interfaces\DBRepositoriesInterface;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamerByIdControllerTest extends TestCase
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
        $mockRepo->shouldReceive('getStreamerById')
            ->andReturnUsing(function (string $id) {
                if ($id === '123') {
                    return [
                        'id'        => '123',
                        'user_name' => 'StreamerUser',
                        'title'     => 'Live now!'
                    ];
                }
                return [];
            });
        $this->app->instance(TwitchApiRepositoryInterface::class, $mockRepo);

    }

    /** @test */
    public function get400WhenMissingId(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/user',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'error' => "Invalid or missing 'id' parameter.",
        ]);
    }

    /** @test */
    public function withInvalidTokenReturns401(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/user',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer invalidtoken']
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    /** @test */
    public function get400WhenInvalidId(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/user',
            ['id' => 'invalid_id'],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'error' => "Invalid or missing 'id' parameter.",
        ]);
    }

    /** @test */
    public function get200WhenValidId(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/user',
            ['id' => '123'],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'id'        => '123',
            'user_name' => 'StreamerUser',
            'title'     => 'Live now!'
        ]);
    }
}
