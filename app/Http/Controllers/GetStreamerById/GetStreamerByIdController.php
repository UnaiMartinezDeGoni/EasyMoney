<?php
// app/Http/Controllers/GetStreamerById/GetStreamerByIdController.php

namespace App\Http\Controllers\GetStreamerById;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamerByIdService;
use App\Exceptions\EmptyOrInvalidIdException;

class GetStreamerByIdController extends Controller
{
    public function __construct(
        private readonly GetStreamerByIdValidator $validator,
        private readonly GetStreamerByIdService   $service
    ) {}

    /**
     * GET /analytics/streamer?id={id}
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $id = $this->validator->validate($request->input('id'));
        } catch (EmptyOrInvalidIdException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                400
            );
        }

        return $this->service->getStreamerById($id);
    }
}
