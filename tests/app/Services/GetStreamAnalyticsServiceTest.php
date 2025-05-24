<?php

namespace Tests\app\Services;

use Tests\TestCase;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Services\GetStreamAnalyticsService;
use Illuminate\Http\JsonResponse;

class GetStreamAnalyticsServiceTest extends TestCase
{
    private TwitchApiRepositoryInterface $repo;
    private GetStreamAnalyticsService    $service;

    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 1) Crea el mock del repositorio
        $this->repo = $this->createMock(TwitchApiRepositoryInterface::class);
        $this->app->instance(TwitchApiRepositoryInterface::class, $this->repo);

        // 2) Resuelve la instancia real del servicio
        $this->service = $this->app->make(GetStreamAnalyticsService::class);
    }

    /** @test */
    public function returns_streams_when_repo_returns_data(): void
    {
        $streams = [
            ['title' => 'Stream 1', 'user_name' => 'User1'],
            ['title' => 'Stream 2', 'user_name' => 'User2'],
        ];

        $this->repo
            ->expects($this->once())
            ->method('getStreams')
            ->willReturn($streams);

        $response = $this->service->getStreams();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($streams, $response->getData(true));
    }

    /** @test */
    public function returns_empty_array_when_repo_returns_nothing(): void
    {
        $this->repo
            ->method('getStreams')
            ->willReturn([]);

        $response = $this->service->getStreams();

        $this->assertSame([], $response->getData(true));
        $this->assertSame(200, $response->getStatusCode());
    }

    /** @test */
    public function returns_empty_array_on_repo_exception(): void
    {
        $this->repo
            ->expects($this->once())
            ->method('getStreams')
            ->will($this->throwException(new \Exception('API failure')));

        $response = $this->service->getStreams();

        $this->assertSame([], $response->getData(true));
        $this->assertSame(200, $response->getStatusCode());
    }
}
