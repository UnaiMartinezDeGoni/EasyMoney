<?php

namespace Tests\app\Services;

use App\Interfaces\DBRepositoriesInterface;
use App\Services\RegisterService;
use App\Exceptions\ServerErrorException;
use Mockery;
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

        $mockRepo = Mockery::mock(DBRepositoriesInterface::class);

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

        $result = $service->register($email);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('api_key', $result);
        $this->assertIsString($result['api_key']);
        $this->assertEquals(16, strlen($result['api_key']));
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

        $mockRepo = Mockery::mock(DBRepositoriesInterface::class);

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

        $result = $service->register($email);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('api_key', $result);
        $this->assertIsString($result['api_key']);
        $this->assertEquals(16, strlen($result['api_key']));
        $this->assertNotEquals($oldApiKey, $result['api_key']);
    }

    /**
     * @test
     */
    public function throwsServerErrorExceptionOnRepositoryFailure(): void
    {
        $email = 'error@example.com';

        $mockRepo = Mockery::mock(DBRepositoriesInterface::class);

        $mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andThrow(new \Exception('DB down'));

        $service = new RegisterService($mockRepo);

        $this->expectException(ServerErrorException::class);

        $service->register($email);
    }
}

