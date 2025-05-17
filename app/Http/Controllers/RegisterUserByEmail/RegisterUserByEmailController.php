<?php

namespace App\Http\Controllers\RegisterUserByEmail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class RegisterUserByEmailController extends Controller
{
    public function index(Request $request)
    {
        header('Content-Type: application/json');

        require_once __DIR__ . '/../../../../funcionesComunes.php';

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['email'])) {
            http_response_code(400);
            echo json_encode(["error" => "The email is mandatory"], JSON_PRETTY_PRINT);
            exit;
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "The email must be a valid email address"], JSON_PRETTY_PRINT);
            exit;
        }

        $email = $data['email'];

        try {
            $mysqli = conectarMysqli();


            $stmt = $mysqli->prepare("SELECT api_key FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result->fetch_assoc();


            $apiKey = generateApiKey();
            if ($user) {
                $stmt = $mysqli->prepare("UPDATE users SET api_key = ? WHERE email = ?");
                $stmt->bind_param("ss", $apiKey, $email);
                $stmt->execute();
            } else {
                $stmt = $mysqli->prepare("INSERT INTO users (email, api_key) VALUES (?, ?)");
                $stmt->bind_param("ss", $email, $apiKey);
                $stmt->execute();
            }

            http_response_code(200);
            echo json_encode(["api_key" => $apiKey], JSON_PRETTY_PRINT);
            exit;

        } catch (Throwable $e) {
            error_log('[RegisterUser] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
            exit;
        }

    }

}



