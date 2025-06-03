<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\InvalidLimitException;
use App\Exceptions\ServerErrorException;
use App\Services\GetEnrichedStreamsService;

class GetEnrichedStreamsController extends BaseController
{
    public function __construct(
        private readonly GetEnrichedStreamsService $service
    ) {}

    public function getEnrichedStreams(Request $request): JsonResponse
    {
        $limitParam = $request->get('limit');

        try {
            $validator  = new GetEnrichedStreamsValidator();
            $cleanLimit = $validator->validate($limitParam);
            $limit = (int) $cleanLimit;
        } catch (InvalidLimitException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                400
            );
        }

        try {
            $result = $this->service->getEnrichedStreams($limit);
            return new JsonResponse($result, 200);
        } catch (ServerErrorException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
