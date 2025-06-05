<?php

namespace Tests\app\Services;

use App\Services\GetStreamerByIdService;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\TwitchUnauthorizedException;
use App\Exceptions\StreamerNotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetStreamerByIdServiceTest extends TestCase
{
    private $mockRepo;
    private GetStreamerByIdService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepo = Mockery::mock(TwitchApiRepositoryInterface::class);
        $this->service = new GetStreamerByIdService($this->mockRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function returnsDataWhenStreamerExists(): void
    {
        $id = '1234';
        $expectedData = [
            'id' => '1234',
            'display_name' => 'StreamerName',
            'description' => 'Some description',
        ];

        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->once()
            ->with($id)
            ->andReturn($expectedData);

        $result = $this->service->getStreamerById($id);

        $this->assertSame($expectedData, $result);
    }

    /** @test */
    public function throwsStreamerNotFoundExceptionWhenDataIsEmpty(): void
    {
        $id = '9999';

        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->once()
            ->with($id)
            ->andReturn([]);

        $this->expectException(StreamerNotFoundException::class);

        $this->service->getStreamerById($id);
    }

    /** @test */
    public function rethrowsTwitchUnauthorizedException(): void
    {
        $id = 'abcd';

        $this->mockRepo
            ->shouldReceive('getStreamerById')
            ->once()
            ->with($id)
            ->andThrow(new TwitchUnauthorizedException());

        $this->expectException(TwitchUnauthorizedException::class);

        $this->service->getStreamerById($id);
    }


}

