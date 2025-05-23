<?php

namespace Tests\app\Token;

use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    protected string $endpoint;
    protected array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->endpoint = '/token';

        $this->headers = ['CONTENT_TYPE' => 'application/json'];
    }

    /**
     * @test
     */
    public function gets400WhenEmailIsMissing(): void
    {
        $response = $this->call(
            'POST',
            $this->endpoint,
            [], [], [], $this->headers,
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
            $this->endpoint,
            [], [], [], $this->headers,
            json_encode([
                'email' => 'invalido',
                'api_key' => '123456'
            ])
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
    public function gets400WhenApiKeyIsMissing(): void
    {
        $response = $this->call(
            'POST',
            $this->endpoint,
            [], [], [], $this->headers,
            json_encode([
                'email' => 'usuario@dominio.com'
            ])
        );

        $this->assertEquals(400, $response->status());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'The api_key is mandatory'], JSON_PRETTY_PRINT),
            $response->getContent()
        );
    }
}
