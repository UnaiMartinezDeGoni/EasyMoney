<?php

namespace App\Http\Controllers\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class TokenController extends Controller
{
    public function generateToken(Request $request)
    {
        require_once __DIR__ . '/../../../../funcionesComunes.php';

        $data = $request->json()->all();

        if (empty($data['email'])) {
            return response()->json([
                'error' => 'The email is mandatory'
            ], 400, ['Content-Type' => 'application/json']);
        }

        if (empty($data['api_key'])) {
            return response()->json([
                'error' => 'The api_key is mandatory'
            ], 400, ['Content-Type' => 'application/json']);
        }

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $api_key = $data['api_key'];

        if (!$email) {
            return response()->json([
                'error' => 'The email must be a valid email address'
            ], 400, ['Content-Type' => 'application/json']);
        }

        try {
            $mysqli = conectarMysqli();

            $stmt = $mysqli->prepare("SELECT id, api_key FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user || $api_key !== $user['api_key']) {
                return response()->json([
                    'error' => 'Unauthorized. API access token is invalid.'
                ], 401, ['Content-Type' => 'application/json']);
            }

            // Verificar si hay sesión activa
            $stmt = $mysqli->prepare(
                "SELECT token FROM sessions WHERE user_id = ? AND expires_at > NOW()"
            );
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            $resultToken = $stmt->get_result();
            $existing = $resultToken->fetch_assoc();
            $stmt->close();

            if ($existing) {
                $stmt = $mysqli->prepare(
                    "UPDATE sessions SET expires_at = NOW() WHERE user_id = ? AND token = ?"
                );
                $stmt->bind_param("is", $user['id'], $existing['token']);
                $stmt->execute();
                $stmt->close();
            }

            // Crear nuevo token
            $new = generateApiToken();

            $stmt = $mysqli->prepare(
                "INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("iss", $user['id'], $new['token'], $new['expires_at']);
            $stmt->execute();
            $stmt->close();

            return response()->json([
                'token' => $new['token']
            ], 200, ['Content-Type' => 'application/json']);

        } catch (Throwable $e) {
            error_log('[ObtenerToken] ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error.'
            ], 500, ['Content-Type' => 'application/json']);
        }
    }
}
