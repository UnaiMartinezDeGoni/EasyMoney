<?php
namespace App\Http\Controllers\GetStreamerById;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamerByIdController extends Controller
{
    /**
     * GET /analytics/streamer?id={id}
     */
    public function index(Request $request): JsonResponse
    {
        // 1) Validar id
        $id = trim((string) $request->input('id', ''));

        if ($id === '' || !ctype_digit($id) || (int) $id < 1) {
            return new JsonResponse(
                ['error' => "Invalid or missing 'id' parameter."],
                400
            );
        }

        // 2) Repositorio (mockeado en tests)
        $repo = app(TwitchApiRepositoryInterface::class);

        try {
            $streamer = $repo->getStreamerById($id);
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Internal server error.'], 500);
        }

        // 3) 404 o 200
        if (empty($streamer)) {
            return new JsonResponse(['error' => 'Streamer not found.'], 404);
        }

        return new JsonResponse($streamer, 200);
    }
}
