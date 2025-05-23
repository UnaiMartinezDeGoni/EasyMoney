<?php
// src/Http/Middleware/AuthenticateToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use App\Helpers\FuncionesComunes;

class AuthenticateToken
{
    /**
     * Comprueba el Bearer token en la cabecera Authorization
     * y lo valida contra la tabla sessions.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1) Conectar a MySQL usando tu helper
        try {
            $mysqli = FuncionesComunes::conectarMysqli();
        } catch (RuntimeException $e) {
            return new JsonResponse(
                ['error' => 'Internal server error.'],
                500
            );
        }

        // 2) Leer y parsear cabecera
        $header = $request->header('Authorization', '');
        if (! preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
            return new JsonResponse(
                ['error' => 'Unauthorized. Token is missing or invalid.'],
                401
            );
        }
        $token = $m[1];

        // 3) Verificar token en BD
        $stmt = $mysqli->prepare(
            "SELECT id FROM sessions WHERE token = ? AND expires_at > NOW()"
        );
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return new JsonResponse(
                ['error' => 'Unauthorized. Token is invalid or expired.'],
                401
            );
        }

        // 4) Si todo ok, continuar
        return $next($request);
    }
}
