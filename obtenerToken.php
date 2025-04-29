<?php
global $mysqli;

header('Content-Type: application/json');
require_once __DIR__ . '/config.php';    // Archivo necesario para la conexión a la base de datos
require_once __DIR__ .'/funcionesComunes.php'; 

// Captura el contenido del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

//Caso en el que no se mande el email
if (!isset($data['email']) || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(["error" => "The email is mandatory"], JSON_PRETTY_PRINT); 
    exit;
}

//Caso en el que no se mande la api_key
if (!isset($data['api_key']) || empty($data['api_key'])) {
    http_response_code(400);
    echo json_encode(["error" => "The api_key is mandatory"], JSON_PRETTY_PRINT);
    exit;
}

//se verifica que la estructura del email sea correcta
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$api_key = $data['api_key'];
if (!$email) {
    http_response_code(400);
    echo json_encode(["error" => "The email must be a valid email address"], JSON_PRETTY_PRINT);
    exit;
}

try {
    // Se conecta a la base de datos y verifica si el usuario ya se encuentra registrado
    $stmt = $mysqli->prepare("SELECT api_key,id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Verifica si $user existe y si el API key proporcionado coincide con el valor de $user['api_key'] 
    if ($user && $api_key === $user['api_key']) {
        $stmt = $mysqli->prepare("SELECT token FROM sessions WHERE user_id = ? AND expires_at > NOW()");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $resultToken = $stmt->get_result();
        $existToken = $resultToken->fetch_assoc();
        $stmt->close();

        //Si existia uno anterior de ese usuario, caduca la validez del token
        if ($existToken) {
            $stmt = $mysqli->prepare("UPDATE sessions SET expires_at = NOW() WHERE user_id = ? AND token = ?");
            $stmt->bind_param("is", $user['id'], $existToken['token']);
            $stmt->execute();
            $stmt->close();
        }

        //Genera el token con la funcion implementada del archivo funcionesComunes.php
        $userToken = generateApiToken();

        //Inserta el token asociandolo al usuario en la base de datos con una validez de 3 dias
        $stmt = $mysqli->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $userToken['token'], $userToken['expires_at']);
        $stmt->execute();
        $stmt->close();
      
		
        echo json_encode(["token" => $userToken['token']], JSON_PRETTY_PRINT);
        http_response_code(200);
        
    } else {    //En el caso de que el la API key sea unvalida
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. API access token is invalid."], JSON_PRETTY_PRINT);
        exit;
    }
} catch (Exception $e) {    //Caso en el que se detecte otro tipo de error
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    exit;
}
