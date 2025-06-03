<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;

class AuthenticateToken
{
    public function __construct(private readonly AuthService $authService) {}

    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization', null);

        if (! $authHeader) {
            $authHeader = $request->server('HTTP_AUTHORIZATION', '');
        }

        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $m) ||
            ! $this->authService->validateToken($m[1])
        ) {
            return new JsonResponse(['error' => 'Unauthorized. Token is invalid or expired.'], 401);
        }

        return $next($request);
    }
}
