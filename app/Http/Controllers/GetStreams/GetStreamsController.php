<?php

namespace App\Http\Controllers\GetStreams;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamAnalyticsService;
use App\Services\AuthService;

class GetStreamsController extends Controller
{
    public function __construct(
        private readonly GetStreamAnalyticsService $service,
        private readonly AuthService $auth
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $header = $request->header('Authorization', '');
        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $header, $m) ||
            ! $this->auth->validateToken($m[1])
        ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
