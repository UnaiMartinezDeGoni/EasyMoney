<?php

namespace Tests\app\Http\Controllers\GetStreams;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use App\Services\StreamAnalyticsService;
use App\Infrastructure\TokenManager;

class GetStreamsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 1) Mock del TokenManager para no tocar BD
        $this->app->instance(TokenManager::class, new class {
            public function validateToken(string $token): bool
            {
                return $token === 'e59a7c4b2d301af8';
            }
        });

        // 2) Mock del StreamAnalyticsService
        $this->app->instance(StreamAnalyticsService::class, \Mockery::mock(StreamAnalyticsService::class, function ($m) {
            $m->shouldReceive('getStreams')->andReturnUsing(function (int $limit) {
                // Devuelve tantos items como $limit
                $out = [];
                for ($i = 1; $i <= $limit; $i++) {
                    $out[] = ['id'=>"s{$i}", 'viewer_count'=>100 + $i];
                }
                return new JsonResponse($out, Response::HTTP_OK);
            });
        }));
    }

    /** @test */
    public function withoutAuthHeader_returns_401(): void
    {
        $resp = $this->call('GET', '/analytics/streams');
        $resp->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function withInvalidToken_returns_401(): void
    {
        $resp = $this->call(
            'GET', '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer bad_token']
        );
        $resp->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function limit_below_min_returns_422(): void
    {
        $resp = $this->call(
            'GET', '/analytics/streams?limit=0',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $resp->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    /** @test */
    public function limit_above_max_returns_422(): void
    {
        $resp = $this->call(
            'GET', '/analytics/streams?limit=101',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $resp->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    /** @test */
    public function limit_not_integer_returns_422(): void
    {
        $resp = $this->call(
            'GET', '/analytics/streams?limit=foo',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $resp->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['limit']);
    }

    /** @test */
    public function default_limit_returns_10_items(): void
    {
        $resp = $this->call(
            'GET', '/analytics/streams',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $resp->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10)
            ->assertJsonFragment(['id' => 's1', 'viewer_count' => 101])
            ->assertJsonFragment(['id' => 's10', 'viewer_count' => 110]);
    }

    /** @test */
    public function custom_limit_returns_correct_number_of_items(): void
    {
        $limit = 5;
        $resp = $this->call(
            'GET', "/analytics/streams?limit={$limit}",
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $resp->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($limit)
            ->assertJsonFragment(['id' => 's5', 'viewer_count' => 105]);
    }

    /** @test */
    public function empty_result_returns_empty_array(): void
    {
        // Rebind service para que devuelva siempre []
        $this->app->instance(StreamAnalyticsService::class, \Mockery::mock(StreamAnalyticsService::class, function ($m) {
            $m->shouldReceive('getStreams')->andReturn(new JsonResponse([], Response::HTTP_OK));
        }));

        $resp = $this->call(
            'GET', '/analytics/streams?limit=3',
            [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer e59a7c4b2d301af8']
        );
        $resp->assertStatus(Response::HTTP_OK)
            ->assertExactJson([]);
    }
}
