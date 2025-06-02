<?php

namespace Tests\app\Services;

use App\Repositories\DB_Repositories;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class RegisterServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function givenNoApiKeyInsertsNewApiKey(): void
    {
        $email = 'newuser@example.com';

        $mockRepo = Mockery::mock(DB_Repositories::class);

        $mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andReturnNull();

        $mockRepo
            ->shouldReceive('insertUser')
            ->once()
            ->with(
                $email,
                Mockery::type('string')
            )
            ->andReturnTrue();

        $mockRepo
            ->shouldReceive('updateApiKey')
            ->never();

        $service = new RegisterService($mockRepo);

        $response = $service->register($email);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('api_key', $content);
        $this->assertIsString($content['api_key']);
        $this->assertEquals(16, strlen($content['api_key']));
    }

    /**
     * @test
     */
    public function givenExistingApiKeyUpdatesApiKey(): void
    {
        $email         = 'existing@example.com';
        $oldApiKey     = 'old_api_key_value';
        $fakeUserArray = [
            'id'      => 123,
            'api_key' => $oldApiKey,
        ];

        $mockRepo = Mockery::mock(DB_Repositories::class);

        $mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andReturn($fakeUserArray);

        $mockRepo
            ->shouldReceive('updateApiKey')
            ->once()
            ->with(
                $email,
                Mockery::type('string')
            )
            ->andReturnTrue();

        $mockRepo
            ->shouldReceive('insertUser')
            ->never();

        $service = new RegisterService($mockRepo);

        $response = $service->register($email);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('api_key', $content);
        $this->assertIsString($content['api_key']);
        $this->assertEquals(16, strlen($content['api_key']));
        $this->assertNotEquals($oldApiKey, $content['api_key']);
    }


}

