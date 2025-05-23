<?php
// src/Http/Controllers/GetStreams/GetStreamsController.php

namespace App\Http\Controllers\GetStreams;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\StreamAnalyticsService;

class GetStreamsController extends Controller
{
    public function __construct(
        private readonly StreamAnalyticsService $service,
        private readonly StreamsValidator        $validator
    ) {}

    /**
     * GET /analytics/streams
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // 1. Valida y sanea
        $data = $this->validator->validate($request);

        // 2. Llama al servicio con el límite validado
        return $this->service->getStreams($data['limit']);
    }
}
