<?php

namespace App\Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController
{
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

        $service = app(RegisterService::class);
        return $service->register($email);
    }
}
