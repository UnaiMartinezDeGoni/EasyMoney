<?php

namespace GetEnrichedStreams;

use app\Services\GetEnrichedStreamsService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Tests de integración del controlador que expone
 *   GET /analytics/streams/enriched
 *
 * Se replica la metodología empleada en otros módulos del proyecto: tres casos
 * (falta parámetro, parámetro inválido y parámetro correcto) y mock del
 * servicio de dominio para aislar la capa HTTP.
 */
class GetEnrichedStreamsControllerTest extends TestCase
{
    private const AUTH = ['HTTP_AUTHORIZATION' => 'Bearer fake-token'];

    /** @test */
    public function givenMissingLimitReturns400(): void
    {
        $response = $this->call('GET', '/analytics/streams/enriched', [], [], [], self::AUTH);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => "Invalid 'limit' parameter.",
        ]);
    }

    /** @test */
    public function givenInvalidLimitReturns400(): void
    {
        $response = $this->call(
            'GET',
            '/analytics/streams/enriched',
            ['limit' => 'invalid_limit'],
            [],
            [],
            self::AUTH
        );

        $response->assertStatus(400);
        $response->assertJson([
            'error' => "Invalid 'limit' parameter.",
        ]);
    }

    /** @test */
    public function givenValidLimitReturns200(): void
    {
        $payload = [[
            'stream_id'         => '1',
            'user_id'           => '1',
            'user_name'         => 'user_name',
            'viewer_count'      => '100',
            'title'             => 'title',
            'user_display_name' => 'user_display_name',
            'profile_image_url' => 'profile_image_url',
        ]];

        $mockService = \Mockery::mock(GetEnrichedStreamsService::class);
        $mockService->shouldReceive('getEnrichedStreams')
                    ->once()
                    ->with(1) // el controlador castea a int
                    ->andReturn(new JsonResponse($payload, 200));

        // Inyectamos el mock en el contenedor
        $this->app->instance(GetEnrichedStreamsService::class, $mockService);

        $response = $this->call(
            'GET',
            '/analytics/streams/enriched',
            ['limit' => '1'],
            [],
            [],
            self::AUTH
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'stream_id',
                'user_id',
                'user_name',
                'viewer_count',
                'title',
                'user_display_name',
                'profile_image_url',
            ],
        ]);
    }
}
