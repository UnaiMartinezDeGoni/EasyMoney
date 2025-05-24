<?php
namespace App\Services;

use App\Helpers\FuncionesComunes;
use RuntimeException;

class AuthService
{
    public function validateToken(string $token): bool
    {
        try {
            $mysqli = FuncionesComunes::conectarMysqli();
        } catch (RuntimeException $e) {
            return false;
        }

        $stmt = $mysqli->prepare(
            "SELECT id FROM sessions WHERE token = ? AND expires_at > NOW()"
        );
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
}
