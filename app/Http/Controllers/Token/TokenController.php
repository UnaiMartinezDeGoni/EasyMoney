<?php

namespace App\Http\Controllers\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function generateToken(Request $request)
    {

        header('Content-Type: application/json');
        require_once __DIR__ . '/../../../../funcionesComunes.php';

        // 1) Leer y validar JSON entrante
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['email'])) {
            http_response_code(400);
            echo json_encode(["error" => "The email is mandatory"], JSON_PRETTY_PRINT);
            exit;
        }
        if (empty($data['api_key'])) {
            http_response_code(400);
            echo json_encode(["error" => "The api_key is mandatory"], JSON_PRETTY_PRINT);
            exit;
        }

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $api_key = $data['api_key'];
        if (!$email) {
            http_response_code(400);
            echo json_encode(["error" => "The email must be a valid email address"], JSON_PRETTY_PRINT);
            exit;
        }

        try {
            // 2) Conectar a la base de datos
            $mysqli = conectarMysqli();

            // 3) Buscar usuario y su api_key
            $stmt = $mysqli->prepare("SELECT id, api_key FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user || $api_key !== $user['api_key']) {
                http_response_code(401);
                echo json_encode(["error" => "Unauthorized. API access token is invalid."], JSON_PRETTY_PRINT);
                exit;
            }

            // 4) Si existe sesión activa, renueva su expires_at
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

            // 5) Generar y guardar nuevo token
            $new = generateApiToken();
            $stmt = $mysqli->prepare(
                "INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("iss", $user['id'], $new['token'], $new['expires_at']);
            $stmt->execute();
            $stmt->close();

            // 6) Responder con el token
            http_response_code(200);
            echo json_encode(["token" => $new['token']], JSON_PRETTY_PRINT);
            exit;

        } catch (Throwable $e) {
            error_log('[ObtenerToken] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
            exit;
        }
    }
}
