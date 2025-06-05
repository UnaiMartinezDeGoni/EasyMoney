<?php

namespace App\Http\Controllers\GetStreams;

use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamAnalyticsService;

class GetStreamsController extends Controller
{
    public function __construct(
        private readonly GetStreamAnalyticsService $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $streams = $this->service->listarStreams();

            $payload = [
                'data' => $streams,
                'meta' => [
                    'total' => count($streams),
                ],
            ];

            return response()->json($payload, 200);
        } catch (TwitchUnauthorizedException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                401
            );
        } catch (ServerErrorException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        }
    }
}
