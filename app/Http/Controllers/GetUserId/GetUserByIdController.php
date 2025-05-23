<?php

namespace App\Http\Controllers\GetUserId;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

Class GetUserByIdController extends Controller{
    public function index(Request $request){

        require_once __DIR__ . '/../../../../funcionesComunes.php';
        header('Content-Type: application/json');

        $streamer_id = isset($_GET['id']) ? (int)$_GET['id'] : -1;

        if ($streamer_id < 1) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid or missing 'id' parameter."], JSON_PRETTY_PRINT);
            exit;
        }

        $access_token = $_GET['access_token'] ?? obtenerTokenTwitch();
        if (empty($access_token)) {
            http_response_code(401);
            echo json_encode(["error" => "Could not obtain a valid access token."], JSON_PRETTY_PRINT);
            exit;
        }

        $respuesta = getStreamerInfo($streamer_id, $access_token);

        if (isset($respuesta['status']) && isset($respuesta['message'])) {
            $status = (int)$respuesta['status'];
            if ($status === 401) {
                http_response_code(401);
                echo json_encode(["error" => "Unauthorized: {$respuesta['message']}"], JSON_PRETTY_PRINT);
                exit;
            }
            http_response_code(404);
            echo json_encode(["error" => "User not found."], JSON_PRETTY_PRINT);
            exit;
        }

        if (!is_array($respuesta) || !array_key_exists('data', $respuesta) || !is_array($respuesta['data'])) {
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
            exit;
        }

        if (empty($respuesta['data'])) {
            http_response_code(404);
            echo json_encode(["error" => "User not found."], JSON_PRETTY_PRINT);
            exit;
        }

        http_response_code(200);
        echo json_encode($respuesta['data'][0], JSON_PRETTY_PRINT);
        exit;

    }
}
