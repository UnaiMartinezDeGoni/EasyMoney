<?php

namespace App\Http\Controllers\Token;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\ServerErrorException;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController
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
            $email  = $data['email'];
            $apiKey = $data['api_key'];
        }

        catch (EmptyEmailException | InvalidEmailException | EmptyApiKeyException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }

        try {
            $result = $this->service->issueToken($email, $apiKey);
            return response()->json(
                $result,
                200,
                [],
                JSON_PRETTY_PRINT
            );
        }
        catch (InvalidApiKeyException $e) {
            return response()->json(
                ['error' => 'Unauthorized. ' . $e->getMessage()],
                401,
                [],
                JSON_PRETTY_PRINT
            );
        }
        catch (ServerErrorException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        }
    }
}
