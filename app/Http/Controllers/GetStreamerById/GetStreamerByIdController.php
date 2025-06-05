<?php

namespace App\Http\Controllers\GetStreamerById;

use App\Exceptions\TwitchUnauthorizedException;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamerByIdService;
use App\Exceptions\EmptyOrInvalidIdException;
use App\Exceptions\StreamerNotFoundException;
use App\Exceptions\ServerErrorException;

class GetStreamerByIdController extends Controller
{
    public function __construct(
        private readonly GetStreamerByIdService $service
    ) {}

    public function getStreamer(Request $request): JsonResponse
    {
        try {
            $validator = new GetStreamerByIdValidator();
            $id = $validator->validate($request->input('id'));
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
        }
        catch (TwitchUnauthorizedException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                401
            );
        }
    }
}

