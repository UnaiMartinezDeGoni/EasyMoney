<?php
global $mysqli;

header('Content-Type: application/json');
require_once 'config.php';
require_once 'funcionesComunes.php';// Conexión a la base de datos

// Captura el contenido del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(["error" => "The email is mandatory"]);
    http_response_code(400);
    exit;
}

if (!isset($data['api_key']) || empty($data['api_key'])) {
    echo json_encode(["error" => "The api_key is mandatory"]);
    http_response_code(400);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$api_key = $data['api_key'];
if (!$email) {
    echo json_encode(["error" => "The email must be a valid email address"]);
    http_response_code(400);
    exit;
}

try {
    $stmt = $mysqli->prepare("SELECT api_key,id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && $api_key === $user['api_key']) {
        $stmt = $mysqli->prepare("SELECT token FROM sessions WHERE user_id = ? AND expires_at > NOW()");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $resultToken = $stmt->get_result();
        $existToken = $resultToken->fetch_assoc();
        $stmt->close();

        if ($existToken) {
            $stmt = $mysqli->prepare("UPDATE sessions SET expires_at = NOW() WHERE user_id = ? AND token = ?");
            $stmt->bind_param("is", $user['id'], $existToken['token']);
            $stmt->execute();
            $stmt->close();
        }

        $userToken = generateApiToken();
        $stmt = $mysqli->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $userToken['token'], $userToken['expires_at']);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["token" => $userToken['token']]);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "Unauthorized. API access token is invalid."]);
        http_response_code(401);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Internal server error."]);
    http_response_code(500);
    exit;
}
