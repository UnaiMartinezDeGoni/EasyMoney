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

    public function generateToken(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        try {
            $this->validator->validate($data);
        } catch (EmptyEmailException | InvalidEmailException | EmptyApiKeyException $e) {
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
