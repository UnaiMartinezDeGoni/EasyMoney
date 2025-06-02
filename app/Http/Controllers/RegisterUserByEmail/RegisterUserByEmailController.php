<?php
namespace App\Http\Controllers\RegisterUserByEmail;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Services\UserRegisterByEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterUserByEmailController
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        $validator = new RegisterUserByEmailValidator();

        try {
            $validator->validate($data);
            $email = $data['email'];
        } catch (EmptyEmailException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (InvalidEmailException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }


        $service = app(UserRegisterByEmailService::class);

        return $service->register($email);
    }
}
