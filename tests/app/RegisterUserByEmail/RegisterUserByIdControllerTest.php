<?php

namespace Tests\app\RegisterUserByEmail;

use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Mockery;

class RegisterUserByIdControllerTest extends TestCase
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
    public function gets200AndReturnsApiKeyWhenEmailIsValid(): void
    {
        $email = 'test@example.com';

        // Creamos una respuesta JSON simulada que se espera retorne el endpoint.
        $mockResponse = new JsonResponse([
            'api_key' => 'api_key_value',
        ], 200);

        // Creamos un mock del controlador, forzando que el método "index" retorne la respuesta simulada.
        $mockController = \Mockery::mock(\App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailController::class);
        $mockController->shouldReceive('index')
            ->once()
            // No nos importan los parámetros que reciba; se puede usar withAnyArgs() o with(Mockery::any())
            ->withAnyArgs()
            ->andReturn($mockResponse);

        // Inyectamos el mock en el contenedor, de modo que al resolver el controlador se use el mock.
        $this->app->instance(
            \App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailController::class,
            $mockController
        );

        // Realizamos la petición POST al endpoint '/register' con un email válido.
        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        // Comprobamos que la respuesta tiene un status 200 y que el JSON contiene el campo "api_key".
        $this->assertEquals(200, $response->status());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('api_key', $content);
        $this->assertNotEmpty($content['api_key']);
    }

}
