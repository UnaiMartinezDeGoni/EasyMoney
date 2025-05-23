<?php

namespace App\Http\Controllers\RegisterUserByEmail;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Http\Controllers\Controller;
use App\Services\UserRegisterByEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterUserByEmailController extends Controller
{
    private RegisterUserByEmailValidator $validator;
    private UserRegisterByEmailService   $userRegisterService;

    public function __construct(
        RegisterUserByEmailValidator $validator,
        UserRegisterByEmailService $userRegisterByEmailService
    ) {
        $this->validator           = $validator;
        $this->userRegisterService = $userRegisterByEmailService;
    }


    public function register(Request $request): JsonResponse
    {
        $data = $request->json()->all();

        try {
            $this->validator->validate($data);
            $email = $data['email'];

            return $this->userRegisterService->register($email);

        } catch (EmptyEmailException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        } catch (InvalidEmailException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
