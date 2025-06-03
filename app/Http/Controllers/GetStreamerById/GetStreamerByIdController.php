<?php
declare(strict_types=1);

namespace App\Http\Controllers\GetStreamerById;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamerByIdController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $id = trim((string)$request->input('id', ''));
        if ($id === '' || !ctype_digit($id) || (int)$id < 1) {
            return response()->json([
                'error' => "Invalid or missing 'id' parameter."
            ], 400);
        }

        try {
            $streamer = app(TwitchApiRepositoryInterface::class)->getStreamerById($id);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Internal server error.'
            ], 500);
        }

        if (empty($streamer)) {
            return response()->json([
                'error' => 'Streamer not found.'
            ], 404);
        }

        return response()->json($streamer, 200);
    }
}
