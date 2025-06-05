<?php

namespace Tests\app\Services;

use App\Services\GetEnrichedStreamsService;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\TwitchUnauthorizedException;
use App\Exceptions\ServerErrorException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Throwable;

class GetEnrichedStreamsServiceTest extends TestCase
{
    private $mockRepo;
    private GetEnrichedStreamsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $this->service = new GetEnrichedStreamsService($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function throwsUnauthorizedWhenGetStreamsThrowsUnauthorized(): void
    {
        $this->mockRepo
            ->shouldReceive('getStreams')
            ->once()
            ->andThrow(new TwitchUnauthorizedException());

        $this->expectException(TwitchUnauthorizedException::class);

        $this->service->getEnrichedStreams(5);
    }


    /** @test */
    public function throwsUnauthorizedWhenGetStreamerByIdThrowsUnauthorized(): void
    {
        $streams = [
            [
                'id'            => 's1',
                'user_id'       => 'u1',
                'user_name'     => 'Streamer1',
                'viewer_count'  => 100,
                'title'         => 'Title1',
            ],
        ];

        $this->mockRepo
            ->shouldReceive('getStreams')
            ->once()
            ->andReturn($streams);

        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->once()
            ->with('u1')
            ->andThrow(new TwitchUnauthorizedException());

        $this->expectException(TwitchUnauthorizedException::class);

        $this->service->getEnrichedStreams(1);
    }

    /** @test */
    public function returnsFormattedListWhenStreamsExist(): void
    {
        $streams = [
            [
                'id'            => 'sA',
                'user_id'       => 'uA',
                'user_name'     => 'Alice',
                'viewer_count'  => 20,
                'title'         => 'Stream A',
            ],
            [
                'id'            => 'sB',
                'user_id'       => 'uB',
                'user_name'     => 'Bob',
                'viewer_count'  => 50,
                'title'         => 'Stream B',
            ],
            [
                'id'            => 'sC',
                'user_id'       => 'uC',
                'user_name'     => 'Carol',
                'viewer_count'  => 10,
                'title'         => 'Stream C',
            ],
        ];

        $this->mockRepo
            ->shouldReceive('getStreams')
            ->once()
            ->andReturn($streams);

        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->with('uA')
            ->andReturn([
                'display_name'      => 'AliceDisplay',
                'profile_image_url' => 'http://example.com/alice.jpg',
            ]);
        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->with('uB')
            ->andReturn([
                'display_name'      => 'BobDisplay',
                'profile_image_url' => 'http://example.com/bob.jpg',
            ]);
        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->with('uC')
            ->andReturn([]);

        $result = $this->service->getEnrichedStreams(2);

        $this->assertCount(2, $result);

        $first = $result[0];
        $this->assertSame('sB', $first['stream_id']);
        $this->assertSame('uB', $first['user_id']);
        $this->assertSame('Bob', $first['user_name']);
        $this->assertSame(50, $first['viewer_count']);
        $this->assertSame('Stream B', $first['title']);
        $this->assertSame('BobDisplay', $first['user_display_name']);
        $this->assertSame('http://example.com/bob.jpg', $first['profile_image_url']);

        $second = $result[1];
        $this->assertSame('sA', $second['stream_id']);
        $this->assertSame('uA', $second['user_id']);
        $this->assertSame('Alice', $second['user_name']);
        $this->assertSame(20, $second['viewer_count']);
        $this->assertSame('Stream A', $second['title']);
        $this->assertSame('AliceDisplay', $second['user_display_name']);
        $this->assertSame('http://example.com/alice.jpg', $second['profile_image_url']);
    }
}
