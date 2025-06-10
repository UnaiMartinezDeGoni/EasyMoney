<?php

namespace Tests\App\Http\Controllers\GetStreams;

use App\Interfaces\DBRepositoriesInterface;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Interfaces\TwitchApiRepositoryInterface;
use Mockery;

class GetStreamsControllerTest extends TestCase
{
    protected string $endpoint = '/analytics/streams';

    protected array $headers = ['CONTENT_TYPE' => 'application/json'];

    protected function setUp(): void
    {
        parent::setUp();

        $mockDBRepo = \Mockery::mock(DBRepositoriesInterface::class);
        $mockDBRepo->shouldReceive('isValidSession')
            ->andReturnUsing(function (string $token) {
                return $token === 'e59a7c4b2d301af8';
            });
        $this->app->instance(DBRepositoriesInterface::class, $mockDBRepo);

        $defaultRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $defaultRepo->shouldReceive('getStreams')->andReturn([]);
        $this->app->instance(TwitchApiRepositoryInterface::class, $defaultRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function withInvalidTokenReturns401(): void
    {
        $response = $this->call(
            'GET',
            $this->endpoint,
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer invalidtoken']
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    /** @test */
    public function validRequestReturnsExpectedData(): void
    {
        $stubStreams = [
            [
                'id'           => '1',
                'title'        => 'Stream 1',
                'user_name'    => 'User1',
                'viewer_count' => 100,
            ],
            [
                'id'           => '2',
                'title'        => 'Stream 2',
                'user_name'    => 'User2',
                'viewer_count' => 200,
            ],
        ];

        $mockRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $mockRepo->shouldReceive('getStreams')
            ->once()
            ->andReturn($stubStreams);
        $this->app->instance(TwitchApiRepositoryInterface::class, $mockRepo);

        $response = $this->call(
            'GET',
            $this->endpoint,
            [], [], [],
            array_merge($this->headers, ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8'])
        );

        $this->assertEquals(Response::HTTP_OK, $response->status());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('meta', $content);
        $this->assertCount(2, $content['data']);
        $this->assertEquals(2, $content['meta']['total']);

        $this->assertEquals('Stream 1', $content['data'][0]['title']);
        $this->assertEquals('User1', $content['data'][0]['user_name']);
        $this->assertEquals(100, $content['data'][0]['viewer_count']);

        $this->assertEquals('Stream 2', $content['data'][1]['title']);
        $this->assertEquals('User2', $content['data'][1]['user_name']);
        $this->assertEquals(200, $content['data'][1]['viewer_count']);
    }
}
