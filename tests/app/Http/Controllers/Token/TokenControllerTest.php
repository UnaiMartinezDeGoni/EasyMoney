<?php

namespace Tests\app\Http\Controllers\Token;

use App\Http\Controllers\Token\TokenValidator;
use App\Repositories\DBRepositories;
use Mockery;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    protected string $endpoint = '/token';
    protected array $headers = ['CONTENT_TYPE' => 'application/json'];

    private $dbRepoMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->endpoint = '/token';
        $this->headers  = ['CONTENT_TYPE' => 'application/json'];

        $this->dbRepoMock = Mockery::mock(DBRepositories::class);
        $this->app->instance(DBRepositories::class, $this->dbRepoMock);

        $this->app->instance(TokenValidator::class, new TokenValidator());


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
                'email'   => 'invalido',
                'api_key' => '123456',
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
                'email' => 'usuario@dominio.com',
            ])
        );

        $this->assertEquals(400, $response->status());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'The api_key is mandatory'], JSON_PRETTY_PRINT),
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function gets200AndTokenWhenServiceIssuesIt(): void
    {

        $this->dbRepoMock
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with('foo@bar.com')
            ->andReturn([
                'id'      => 1,
                'api_key' => 'secret-key',
            ]);

        $this->dbRepoMock
            ->shouldReceive('getActiveSession')
            ->once()
            ->with(1)
            ->andReturn('existing-token');

        $this->dbRepoMock
            ->shouldReceive('refreshSession')
            ->once()
            ->with(1, 'existing-token')
            ->andReturnTrue();


        $payload = [
            'email'   => 'foo@bar.com',
            'api_key' => 'secret-key',
        ];

        $response = $this->call(
            'POST',
            $this->endpoint,
            [], [], [], $this->headers,
            json_encode($payload)
        );

        $this->assertEquals(200, $response->status());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['token' => 'existing-token'], JSON_PRETTY_PRINT),
            $response->getContent()
        );
    }
}
