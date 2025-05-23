<?php

namespace TwitchAnalytics\Tests\app\Services;

use TwitchAnalytics\Tests\TestCase;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Services\StreamAnalyticsService;
use Illuminate\Http\JsonResponse;

class StreamAnalyticsServiceTest extends TestCase
{
    private TwitchApiRepositoryInterface $repo;
    private StreamAnalyticsService         $service;

    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Mock del repositorio
        $this->repo = $this->createMock(TwitchApiRepositoryInterface::class);
        $this->app->instance(TwitchApiRepositoryInterface::class, $this->repo);

        $this->service = $this->app->make(StreamAnalyticsService::class);
    }

    /**
     * @test
     */
    public function returnsStreamsWhenRepoProvidesData(): void
    {
        $limit = 4;
        $data = [
            ['id'=>'a','viewer_count'=>1],
            ['id'=>'b','viewer_count'=>2],
            ['id'=>'c','viewer_count'=>3],
            ['id'=>'d','viewer_count'=>4],
        ];

        $this->repo->expects($this->once())
            ->method('getStreams')
            ->with($limit)
            ->willReturn($data);

        $response = $this->service->getStreams($limit);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($data, $response->getData(true));
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenRepoReturnsEmpty(): void
    {
        $this->repo->method('getStreams')->willReturn([]);

        $response = $this->service->getStreams(7);

        $this->assertSame([], $response->getData(true));
    }

    /**
     * @test
     */
    public function catchesRepoExceptionAndReturnsEmptyArray(): void
    {
        $this->repo->expects($this->once())
            ->method('getStreams')
            ->will($this->throwException(new \Exception('boom')));

        $response = $this->service->getStreams(5);

        // Asumimos que la impl de repo atrapa, pero si no:
        $this->assertSame([], $response->getData(true));
    }

    /**
     * @test
     */
    public function handlesLargeLimitsCorrectly(): void
    {
        $limit = 100;
        $data = array_fill(0, $limit, ['id'=>'x','viewer_count'=>0]);

        $this->repo->expects($this->once())
            ->method('getStreams')
            ->with($limit)
            ->willReturn($data);

        $response = $this->service->getStreams($limit);

        $this->assertCount($limit, $response->getData(true));
    }
}
