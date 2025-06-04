<?php

namespace Tests\app\Services;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\GetStreamAnalyticsService;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamAnalyticsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function listStreamsCallsRepositoryAndReturnsData(): void
    {
        $stubStreams = [
            [
                'id'           => 'abc123',
                'title'        => 'A Single Test Stream',
                'user_name'    => 'UserX',
                'viewer_count' => 50,
            ],
        ];

        $repoMock = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repoMock->shouldReceive('getStreams')
            ->once()
            ->andReturn($stubStreams);

        $service = new GetStreamAnalyticsService($repoMock);

        $result = $service->listarStreams();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('abc123', $result[0]['id']);
        $this->assertEquals('A Single Test Stream', $result[0]['title']);
    }

    /** @test */
    public function listStreamsReturnsEmptyArrayWhenRepositoryThrowsException(): void
    {
        $repoMock = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repoMock->shouldReceive('getStreams')
            ->once()
            ->andThrow(new \Exception('Twitch connection failed'));

        $service = new GetStreamAnalyticsService($repoMock);

        $result = $service->listarStreams();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
