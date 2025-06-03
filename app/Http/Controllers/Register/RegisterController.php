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

    public function __construct(RegisterService $service)
    {
        $this->service = $service;
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        $validator = app(RegisterValidator::class);

        try {
            $validator->validate($data);
            $email = $data['email'];
        } catch (EmptyEmailException | InvalidEmailException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }

        try {
            $result = $this->service->register($email);
            return new JsonResponse(
                $result,
                200,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (ServerErrorException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        }
    }
}
