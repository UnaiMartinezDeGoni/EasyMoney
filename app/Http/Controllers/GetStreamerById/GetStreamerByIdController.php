<?php
declare(strict_types=1);

namespace App\Http\Controllers\GetStreamerById;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GetStreamerByIdService;
use App\Http\Controllers\GetStreamerById\GetStreamerByIdValidator;
use App\Exceptions\EmptyOrInvalidIdException;

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
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }

        return $this->service->getStreamerById($id);
    }
}

