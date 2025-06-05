<?php

namespace App\Http\Controllers\GetStreams;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamAnalyticsService;

class GetStreamsController extends Controller
{
    public function __construct(
        private readonly GetStreamAnalyticsService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $streams = $this->service->listarStreams();

        $payload = [
            'data' => $streams,
            'meta' => [
                'total' => count($streams),
            ],
        ];

        return response()->json($payload, 200);
    }
}
