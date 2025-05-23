<?php

declare(strict_types=1);

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Exceptions\InvalidLimitException;
use App\Services\GetEnrichedStreamsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Controlador HTTP para el endpoint
 *   GET /analytics/streams/enriched?limit={n}
 *
 * Valida el parámetro de entrada y delega la lógica de negocio en
 * {@see GetEnrichedStreamsService}.  Se inspira en el estilo de RegisterUser
 * pero sin copiarlo literalmente.
 */
class GetEnrichedStreamsController extends BaseController
{
    /**
     * @param GetEnrichedStreamsService  $service   Caso de uso principal.
     * @param GetEnrichedStreamsValidator $validator  Encapsula la validación del request.
     */
    public function __construct(
        private readonly GetEnrichedStreamsService $service,
        private readonly GetEnrichedStreamsValidator $validator
    ) {
    }

    /**
     * Endpoint principal.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEnrichedStreams(Request $request): JsonResponse
    {
        try {
            // 1) Validación (puede lanzar InvalidLimitException)
            $limit = $this->validator->validateLimit($request->query('limit'));

            // 2) Delegamos al servicio y devolvemos la respuesta tal cual
            return $this->service->getEnrichedStreams($limit);
        } catch (InvalidLimitException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}