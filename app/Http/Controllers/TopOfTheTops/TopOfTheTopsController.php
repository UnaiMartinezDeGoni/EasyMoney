<?php

namespace App\Http\Controllers\TopOfTheTops;

use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;
use App\Exceptions\InvalidSinceParameterException;
use App\Services\TopOfTheTopsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class TopOfTheTopsController extends BaseController
{
    private TopOfTheTopsService $service;
    private TopOfTheTopsValidator $validator;

    public function __construct(
        TopOfTheTopsService $service,
        TopOfTheTopsValidator $validator
    ) {
        $this->service   = $service;
        $this->validator = $validator;
    }

    public function index(Request $request): JsonResponse
    {
        $raw = $request->query('since');

        if (isset($raw)) {
            try {
                $this->validator->validate(['since' => $raw]);
                $since = (int) $raw;
            } catch (InvalidSinceParameterException $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 400);
            }
        } else {
            $since = 600;
        }

        try {
            $videos = $this->service->getTopVideos($since);
        } catch (TwitchUnauthorizedException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                401
            );
        } catch (ServerErrorException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

        if (empty($videos)) {
            return response()->json([
                'error' => 'Not Found. No data available.'
            ], 404);
        }

        return response()->json(array_values($videos), 200);
    }
}
