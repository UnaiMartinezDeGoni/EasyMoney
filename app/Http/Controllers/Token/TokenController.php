<?php

namespace App\Http\Controllers\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\InvalidApiKeyException;
use App\Services\TokenService;
use Throwable;

class TokenController extends Controller
{
    private TokenService $service;

    public function __construct(TokenService $service)
    {
        $this->service = $service;
    }

    public function generateToken(Request $request)
    {
        $data = $request->json()->all();

        try {
            (new TokenValidator())->validate($data);
            $token = $this->service->issueToken($data['email'], $data['api_key']);

            return response()->json(['token' => $token], 200);

        } catch (EmptyEmailException | InvalidEmailException | EmptyApiKeyException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (InvalidApiKeyException $e) {
            return response()->json(['error' => 'Unauthorized. ' . $e->getMessage()], 401);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }
}
