<?php

namespace App\Http\Controllers\Token;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Http\Controllers\Controller;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    private TokenService $service;
    private TokenValidator $validator;

    public function __construct(TokenService $service, TokenValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * En este método ya NO atrapamos la excepción de InvalidApiKeyException,
     * porque quien maneja la autorización es directamente el servicio.
     * El controlador sólo valida que el email y api_key estén presentes y bien formados,
     * y luego “retorna” lo que el servicio produce.
     */
    public function generateToken(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        // 1) Validar campos: si falta email, email inválido o falta api_key → 400
        try {
            $this->validator->validate($data);
        }
        catch (EmptyEmailException | InvalidEmailException | EmptyApiKeyException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }

        return $this->service->issueToken($data['email'], $data['api_key']);
    }
}
