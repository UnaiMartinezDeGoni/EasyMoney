<?php

namespace App\Http\Controllers\GetStreams;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamAnalyticsService;
use App\Services\AuthService;
use App\Http\Controllers\GetStreams\StreamsValidator;
use Illuminate\Validation\ValidationException;

class GetStreamsController extends Controller
{
    public function __construct(
        private readonly GetStreamAnalyticsService $service,
        private readonly AuthService                $auth,
        private readonly StreamsValidator           $validator
    ) {}

    /**
     * GET /analytics/streams
     */
    public function __invoke(Request $request): JsonResponse
    {
        // 1) Autenticación
        $header = $request->header('Authorization', '');
        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $header, $m) ||
            ! $this->auth->validateToken($m[1])
        ) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        // 2) Validación de 'limit'
        try {
            $limit = $this->validator->validate($request);
        } catch (ValidationException $e) {
            return new JsonResponse($e->errors(), 422);
        }

        // 3) Lógica de negocio
        return $this->service->getStreams($limit);
    }
}
