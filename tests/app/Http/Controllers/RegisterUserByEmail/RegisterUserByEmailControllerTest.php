<?php

namespace Tests\app\Http\Controllers\RegisterUserByEmail;

use App\Repositories\DB_Repositories;
use Mockery;
use Tests\TestCase;

class RegisterUserByEmailControllerTest extends TestCase
{

    /**
     * @test
     */
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

    /**
     * @test
     */
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
            json_encode(['error' => 'The email must be a valid email address'], JSON_PRETTY_PRINT),
            $response->getContent()
        );
    }


    /**
     * @test
     */
    public function gets200AndReturnsApiKeyWhenEmailIsValidAndUserDoesNotExist(): void
    {
        $email = 'test@example.com';

        $mockRepo = Mockery::mock(DB_Repositories::class);

        $mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);

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

        $this->app->instance(DB_Repositories::class, $mockRepo);

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
        $this->assertNotEmpty($content['api_key']);
    }

    /**
     * @test
     */
    public function gets200AndReturnsApiKeyWhenEmailIsValidAndUserExists(): void
    {
        $email = 'existing@example.com';


        $mockRepo = Mockery::mock(DB_Repositories::class);

        $mockRepo
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with($email)
            ->andReturn([
                'id'      => 42,
                'api_key' => 'old_api_key_value',
            ]);

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

        $this->app->instance(DB_Repositories::class, $mockRepo);

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
        $this->assertNotEmpty($content['api_key']);
    }
}
