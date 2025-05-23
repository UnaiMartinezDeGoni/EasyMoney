<?php

namespace Tests\app\Http\Controllers\RegisterUserByEmail;

use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class RegisterUserByEmailControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
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
    public function gets200AndReturnsApiKeyWhenEmailIsValid(): void
    {
        $email = 'test@example.com';

        $mockResponse = new JsonResponse([
            'api_key' => 'api_key_value',
        ], 200);

        $mockController = Mockery::mock(\App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailController::class);
        $mockController
            ->shouldReceive('register')
            ->once()
            ->withAnyArgs()
            ->andReturn($mockResponse);

        $this->app->instance(
            \App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailController::class,
            $mockController
        );

        $response = $this->call(
            'POST',
            '/register',
            [], [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        $this->assertEquals(200, $response->status());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('api_key', $content);
        $this->assertNotEmpty($content['api_key']);
    }
}
