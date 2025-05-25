<?php
declare(strict_types=1);

namespace App\Http\Controllers\GetStreamerById;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamerByIdController extends Controller
{
    /**
     * GET /analytics/streamer?id={id}
     */
    public function index(Request $request): JsonResponse
    {
        // Validate 'id' parameter
        $id = trim((string) $request->input('id', ''));
        if ($id === '' || !ctype_digit($id) || (int) $id < 1) {
            return response()->json([
                'error' => "Invalid or missing 'id' parameter."
            ], 400);
        }

        // Fetch data from repository
        try {
            $streamer = app(TwitchApiRepositoryInterface::class)->getStreamerById($id);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Internal server error.'
            ], 500);
        }

        // If not found, return 404
        if (empty($streamer)) {
            return response()->json([
                'error' => 'Streamer not found.'
            ], 404);
        }

        // Return streamer data
        return response()->json($streamer, 200);
    }
}
