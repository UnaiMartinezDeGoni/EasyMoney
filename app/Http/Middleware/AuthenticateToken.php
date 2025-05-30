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
        // 1) Intentamos leer el header normalmente
        $authHeader = $request->header('Authorization', null);

        // 2) Si no vino por header(), buscamos en server (HTTP_AUTHORIZATION)
        if (! $authHeader) {
            $authHeader = $request->server('HTTP_AUTHORIZATION', '');
        }

        // 3) Comprobamos formato y validación
        if (
            ! preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $m) ||
            ! $this->authService->validateToken($m[1])
        ) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
