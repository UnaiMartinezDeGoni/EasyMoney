<?php

namespace Tests\app\Http\Controllers\TopOfTheTops;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\TopOfTheTopsService;
use App\Services\AuthService;

class TopOfTheTopsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock de AuthService para que la autenticación funcione sin tocar la BD.
        $mockAuth = \Mockery::mock(AuthService::class);
        $mockAuth->shouldReceive('validateToken')
                 ->andReturnUsing(fn(string $token) => $token === 'e59a7c4b2d301af8');
        $this->app->instance(AuthService::class, $mockAuth);

        // Mock de TopOfTheTopsService: simula devolver datos según el valor de 'since'
        $mockService = \Mockery::mock(TopOfTheTopsService::class);
        $mockService->shouldReceive('getTopVideos')
                    ->andReturnUsing(function (int $since) {
                        if ($since === 600) {
                            $items = array_map(fn($i) => ['id' => "v{$i}", 'views' => 100 + $i], range(1, 5));
                        } elseif ($since === 120) {
                            $items = array_map(fn($i) => ['id' => "v{$i}", 'views' => 200 + $i], range(1, 2));
                        } else {
                            $items = [];
                        }
                        return new JsonResponse($items, Response::HTTP_OK);
                    });
        $this->app->instance(TopOfTheTopsService::class, $mockService);
    }

    /** @test */
    public function invalidSinceParameterReturns400(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops?since=abc',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
                 ->assertJson([
                     "error" => "Bad Request. Invalid or missing parameters: 'since' must be a positive integer."
                 ]);
    }

    /** @test */
    public function defaultSinceReturnsExpectedItems(): void
    {
        // Al no enviar 'since', se usa el valor por defecto 600 (5 items)
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(5)
                 ->assertJsonFragment(['id' => 'v1', 'views' => 101]);
    }

    /** @test */
    public function customSinceParameterReturnsExpectedItems(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/topsofthetops?since=120',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['id' => 'v1', 'views' => 201]);
    }

    /** @test */
    public function emptyResultReturns404(): void
    {
        // Rebindea el servicio para simular un resultado vacío
        $this->app->instance(TopOfTheTopsService::class, \Mockery::mock(TopOfTheTopsService::class, function ($m) {
            $m->shouldReceive('getTopVideos')->andReturn(new JsonResponse([], Response::HTTP_OK));
        }));

        $response = $this->call(
            'GET',
            '/analytics/topsofthetops?since=120',
            [], [], [],
            ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $response->assertStatus(Response::HTTP_NOT_FOUND)
                 ->assertExactJson(["error" => "Not Found. No data available."]);
    }
}
