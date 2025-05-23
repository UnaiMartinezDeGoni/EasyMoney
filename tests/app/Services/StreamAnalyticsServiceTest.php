<?php

namespace Tests\app\Services;

use Tests\TestCase;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Services\StreamAnalyticsService;
use Illuminate\Http\JsonResponse;

class StreamAnalyticsServiceTest extends TestCase
{
    private TwitchApiRepositoryInterface $repo;
    private StreamAnalyticsService       $service;

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

        // Instancia real del servicio
        $this->service = $this->app->make(StreamAnalyticsService::class);
    }

    /** @test */
    public function returns_streams_when_repo_provides_data(): void
    {
        $data = [
            ['id' => 'a', 'viewer_count' => 1],
            ['id' => 'b', 'viewer_count' => 2],
        ];

        $this->repo
            ->expects($this->once())
            ->method('getStreams')
            ->with(2)
            ->willReturn($data);

        $resp = $this->service->getStreams(2);

        $this->assertInstanceOf(JsonResponse::class, $resp);
        $this->assertSame(200, $resp->getStatusCode());
        $this->assertSame($data, $resp->getData(true));
    }

    /** @test */
    public function returns_empty_array_when_repo_returns_empty(): void
    {
        $this->repo->method('getStreams')->willReturn([]);

        $resp = $this->service->getStreams(3);
        $this->assertSame([], $resp->getData(true));
    }

    /** @test */
    public function catches_repo_exception_and_returns_empty_array(): void
    {
        $this->repo
            ->expects($this->once())
            ->method('getStreams')
            ->will($this->throwException(new \Exception('boom')));

        $resp = $this->service->getStreams(4);
        $this->assertSame([], $resp->getData(true));
    }

    /** @test */
    public function handles_large_limits_correctly(): void
    {
        $limit = 100;
        $data  = array_fill(0, $limit, ['id' => 'x', 'viewer_count' => 0]);

        $this->repo
            ->expects($this->once())
            ->method('getStreams')
            ->with($limit)
            ->willReturn($data);

        $resp = $this->service->getStreams($limit);
        $this->assertCount($limit, $resp->getData(true));
    }
}
