<?php

namespace Tests\app\RegisterUserByEmail;

use Tests\TestCase;

class RegisterUserByIdControllerTest extends TestCase
{
    /**
     * @test
     */
    public function gets400WhenEmailIsMissing(): void
    {
        // Simula una petición POST con Content-Type JSON y sin datos en el cuerpo
        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        // Comprobamos que el estatus de la respuesta sea 400 y que se reciba el mensaje de error esperado
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
        // Simula una petición POST con Content-Type JSON y cuerpo con email inválido
        $response = $this->call(
            'POST',
            '/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'invalido'])
        );


        // Comprobamos que se devuelva 400 y el mensaje de error correspondiente
        $this->assertEquals(400, $response->status());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'The email must be a valid email address'], JSON_PRETTY_PRINT),
            $response->getContent()
        );
    }
}
