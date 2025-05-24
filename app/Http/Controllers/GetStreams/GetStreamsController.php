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
        private readonly AuthService           $auth
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $header = $request->header('Authorization', '');
        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $header, $m) ||
            ! $this->auth->validateToken($m[1])
        ) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return $this->service->getStreams();
    }
}
