<?php

namespace Tests\App\Http\Controllers\Register;

use App\Repositories\DBRepositories;
use Mockery;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    private $mockRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepo = Mockery::mock(DBRepositories::class);

        $this->app->instance(DBRepositories::class, $this->mockRepo);


    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function gets400WhenEmailIsMissing(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(400, $response->status());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'The email is mandatory'], JSON_PRETTY_PRINT),
            $response->getContent()
        );
    }

    /** @test */
    public function gets400WhenEmailIsInvalid(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'invalido'])
        );

        $this->assertEquals(400, $response->status());
        $this->assertJsonStringEqualsJsonString(
            json_encode(
                ['error' => 'The email must be a valid email address'],
                JSON_PRETTY_PRINT
            ),
            $response->getContent()
        );
    }

    /** @test */
    public function gets200AndReturnsApiKeyWhenEmailIsValidAndUserDoesNotExist(): void
    {
        $email = 'test@example.com';

        $this->mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andReturnNull();

        $this->mockRepo
            ->shouldReceive('insertUser')
            ->once()
            ->with($email, Mockery::type('string'))
            ->andReturnTrue();

        $this->mockRepo
            ->shouldReceive('updateApiKey')
            ->never();

        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        $this->assertEquals(200, $response->status());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('api_key', $content);
        $this->assertIsString($content['api_key']);
        $this->assertEquals(16, strlen($content['api_key']));
    }

    /** @test */
    public function gets200AndReturnsApiKeyWhenEmailIsValidAndUserExists(): void
    {
        $email = 'existing@example.com';
        $oldApiKey = 'oldapikeyval';

        $this->mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andReturn([
                'id'      => 42,
                'api_key' => $oldApiKey,
            ]);

        $this->mockRepo
            ->shouldReceive('updateApiKey')
            ->once()
            ->with($email, Mockery::type('string'))
            ->andReturnTrue();

        $this->mockRepo
            ->shouldReceive('insertUser')
            ->never();

        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        $this->assertEquals(200, $response->status());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('api_key', $content);
        $this->assertIsString($content['api_key']);
        $this->assertEquals(16, strlen($content['api_key']));
        $this->assertNotEquals($oldApiKey, $content['api_key']);
    }
}
