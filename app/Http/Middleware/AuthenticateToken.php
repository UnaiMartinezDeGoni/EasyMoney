<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;

class AuthenticateToken
{
    public function __construct(private readonly AuthService $auth) {}

    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');
        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $header, $m) ||
            ! $this->auth->validateToken($m[1])
        ) {
            return new JsonResponse(
                ['error' => 'Unauthorized. Token is missing or invalid.'],
                401
            );
        }

        return $next($request);
    }
}
