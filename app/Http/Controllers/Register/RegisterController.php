<?php

namespace App\Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\ServerErrorException;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController
{
    private RegisterService $service;
    private RegisterValidator $validator;

    public function __construct(RegisterService $service, RegisterValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        try {
            $this->validator->validate($data);
            $email = $data['email'];
        }
        catch (EmptyEmailException | InvalidEmailException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }

        try {
            $result = $this->service->register($email);
            return response()->json(
                $result,
                200,
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
