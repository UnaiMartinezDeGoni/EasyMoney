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
        // Llamamos al servicio y obtenemos un array de streams (o vacío)
        $streams = $this->service->listarStreams();

        // Formateamos la respuesta: data + meta
        $payload = [
            'data' => $streams,
            'meta' => [
                'total' => count($streams),
            ],
        ];

        return response()->json($payload, 200);
    }
}
