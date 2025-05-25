<?php
// file: app/Http/Controllers/TopOfTheTops/TopOfTheTopsController.php
declare(strict_types=1);

namespace App\Http\Controllers\TopOfTheTops;

use App\Services\TopOfTheTopsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class TopOfTheTopsController extends BaseController
{
    public function __construct(
        private readonly TopOfTheTopsService $service
    ) {}

    /**
     * GET /analytics/topsofthetops?since={n}
     */
    public function index(Request $request): JsonResponse
    {
        // 1) Validamos `since`
        $raw = $request->query('since');
        if (isset($raw)) {
            if (!ctype_digit($raw) || (int)$raw <= 0) {
                return new JsonResponse([
                    'error' => "Bad Request. Invalid or missing parameters: 'since' must be a positive integer."
                ], 400);
            }
            $since = (int)$raw;
        } else {
            $since = 600;
        }

        // 2) Llamamos al servicio
        $resp  = $this->service->getTopVideos($since);      // devuelve JsonResponse
        $items = $resp->getData(true);                     // obtenemos array

        // 3) Si vacío → 404 con el mensaje exacto
        if (empty($items)) {
            return new JsonResponse([
                'error' => 'Not Found. No data available.'
            ], 404);
        }

        // 4) Datos OK → 200
        return new JsonResponse($items, 200);
    }
}
