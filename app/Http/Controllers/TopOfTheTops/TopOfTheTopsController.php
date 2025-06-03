<?php

namespace App\Http\Controllers\TopOfTheTops;

use App\Services\TopOfTheTopsService;
use App\Http\Controllers\TopOfTheTops\TopOfTheTopsValidator;
use App\Exceptions\InvalidSinceParameterException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class TopOfTheTopsController extends BaseController
{
    public function __construct(
        private readonly TopOfTheTopsService $service,
        private readonly TopOfTheTopsValidator $validator
    ) {}

    public function index(Request $request): JsonResponse
    {
        $raw = $request->query('since');

        if (isset($raw)) {
            try {
                $this->validator->validate(['since' => $raw]);
                $since = (int) $raw;
            } catch (InvalidSinceParameterException $e) {
                return new JsonResponse([
                    'error' => $e->getMessage()
                ], 400);
            }
        } else {
            $since = 600;
        }

        try {
            $videos = $this->service->getTopVideos($since);
        } catch (\RuntimeException $e) {

            return new JsonResponse([
                'error' => $e->getMessage()
            ], 500);
        }

        if (empty($videos)) {
            return new JsonResponse([
                'error' => 'Not Found. No data available.'
            ], 404);
        }

        return new JsonResponse(array_values($videos), 200);
    }


}
