<?php

declare(strict_types=1);

namespace App\Http\Controllers\GetEnrichedStreams;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetEnrichedStreamsController extends BaseController
{
    public function getEnrichedStreams(Request $request): JsonResponse
    {
        $limitParam = $request->get('limit');

        if ($limitParam === null || !is_numeric($limitParam)) {
            return new JsonResponse([
                'error' => "Invalid 'limit' parameter.",
            ], 400);
        }

        $limit = (int) $limitParam;

        return new JsonResponse([
            'data' => [],
            'meta' => [
                'limit' => $limit,
                'total' => 0,
            ],
        ], 200);
    }
}
