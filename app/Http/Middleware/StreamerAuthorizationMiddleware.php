<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;

class StreamerAuthorizationMiddleware
{
    public function __construct(private readonly AuthService $authService) {}

    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        
        if (! $authHeader) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $m)
            || ! $this->authService->validateToken($m[1])
        ) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
