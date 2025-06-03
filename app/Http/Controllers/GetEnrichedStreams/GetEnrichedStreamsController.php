<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\InvalidLimitException;
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
        } catch (InvalidLimitException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                400
            );
        }

        $limit = (int) $cleanLimit;


        return $this->service->getEnrichedStreams($limit);
    }
}
