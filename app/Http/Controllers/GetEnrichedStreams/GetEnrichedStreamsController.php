<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Exceptions\TwitchUnauthorizedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\InvalidLimitException;
use App\Exceptions\ServerErrorException;
use App\Services\GetEnrichedStreamsService;

class GetEnrichedStreamsController extends BaseController
{
    private GetEnrichedStreamsService $service;
    private GetEnrichedStreamsValidator $validator;

    public function __construct(
        GetEnrichedStreamsService $service,
        GetEnrichedStreamsValidator $validator
    ) {
        $this->service   = $service;
        $this->validator = $validator;
    }

    public function getEnrichedStreams(Request $request): JsonResponse
    {
        $limitParam = $request->get('limit');

        try {
            $cleanLimit = $this->validator->validate($limitParam);
            $limit      = (int) $cleanLimit;
        } catch (InvalidLimitException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400
            );
        }

        try {
            $result = $this->service->getEnrichedStreams($limit);
            return response()->json($result, 200);
        } catch (ServerErrorException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500
            );
        } catch (TwitchUnauthorizedException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                401
            );
        }
    }
}
