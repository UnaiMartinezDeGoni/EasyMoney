<?php

namespace App\Http\Controllers\GetStreamerById;

use App\Exceptions\EmptyOrInvalidIdException;
use App\Exceptions\StreamerNotFoundException;
use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;
use App\Services\GetStreamerByIdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;


class GetStreamerByIdController extends Controller
{
    private GetStreamerByIdService $service;
    private GetStreamerByIdValidator $validator;

    public function __construct(GetStreamerByIdService $service, GetStreamerByIdValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    public function getStreamer(Request $request): JsonResponse
    {
        try {
            $id = $this->validator->validate($request->input('id'));
        } catch (EmptyOrInvalidIdException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }

        try {
            $streamerData = $this->service->getStreamerById($id);
            return response()->json($streamerData, 200, [], JSON_PRETTY_PRINT);
        } catch (StreamerNotFoundException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                404,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (ServerErrorException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (TwitchUnauthorizedException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                401
            );
        }
    }
}
