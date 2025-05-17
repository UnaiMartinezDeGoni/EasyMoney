<?php

namespace App\Http\Controllers\RegisterUserByEmail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class RegisterUserByEmailController extends Controller
{
    public function index(Request $request)
    {
        require_once __DIR__ . '/../../../../funcionesComunes.php';

        $data = $request->json()->all();

        $validator = new RegisterUserByEmailValidator();

        try {
            $validator->validate($data);
        } catch (\RuntimeException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
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

            return response()->json(
                ["api_key" => $apiKey],
                200,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (Throwable $e) {
            error_log('[RegisterUser] ' . $e->getMessage());
            return response()->json(
                ["error" => "Internal server error."],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        }
    }
}
