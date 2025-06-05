<?php

namespace Tests\app\Services;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\TopOfTheTopsService;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Helpers\FuncionesComunes;

class TopOfTheTopsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function testGetTopVideosReturnsArrayWhenRepositoryProvidesData(): void
    {
        $stubVideos = [
            [
                'id'           => 'v1',
                'viewer_count' => 100,
                'user_login'   => 'user1',
            ],
            [
                'id'           => 'v2',
                'viewer_count' => 200,
                'user_login'   => 'user2',
            ],
        ];

        $repoMock = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repoMock
            ->shouldReceive('getTopVideos')
            ->once()
            ->with(5)
            ->andReturn($stubVideos);

        $service = new TopOfTheTopsService($repoMock);

        $result = $service->getTopVideos(5);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertEquals('v1', $result[0]['id']);
        $this->assertEquals(100,  $result[0]['viewer_count']);
        $this->assertEquals('https://twitch.tv/user1', $result[0]['url']);

        $this->assertEquals('v2', $result[1]['id']);
        $this->assertEquals(200,  $result[1]['viewer_count']);
        $this->assertEquals('https://twitch.tv/user2', $result[1]['url']);
    }

    /** @test */
    public function testGetTopVideosThrowsRuntimeExceptionWhenRepositoryThrows(): void
    {
        $repoMock = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repoMock
            ->shouldReceive('getTopVideos')
            ->once()
            ->with(10)
            ->andThrow(new \Exception('API down'));

        $service = new TopOfTheTopsService($repoMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error fetching top videos.');

        $service->getTopVideos(10);
    }

    /** @test */
    public function testGetTopVideosThrowsRuntimeExceptionWhenNoDataReturned(): void
    {
        $repoMock = Mockery::mock(TwitchApiRepositoryInterface::class);
        $repoMock
            ->shouldReceive('getTopVideos')
            ->once()
            ->with(0)
            ->andReturn([]);

        $service = new TopOfTheTopsService($repoMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not Found. No data available.');

        $service->getTopVideos(0);
    }
}
