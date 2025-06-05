<?php

namespace App\Http\Controllers\TopOfTheTops;

use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;
use App\Services\TopOfTheTopsService;
use App\Exceptions\InvalidSinceParameterException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class TopOfTheTopsController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        // Obtenemos el validador y el servicio directamente del contenedor
        $validator = app(TopOfTheTopsValidator::class);
        $service   = app(TopOfTheTopsService::class);

        $raw = $request->query('since');

        if (isset($raw)) {
            try {
                $validator->validate(['since' => $raw]);
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
            $videos = $service->getTopVideos($since);
        } catch (TwitchUnauthorizedException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                401
            );
        } catch (ServerErrorException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Servidor encontró un error interno.'
            ], 500);
        }

        if (empty($videos)) {
            return response()->json([
                'error' => 'Not Found. No data available.'
            ], 404);
        }

        // Devolvemos el array de vídeos en la raíz del JSON
        return response()->json(array_values($videos), 200);
    }
}
