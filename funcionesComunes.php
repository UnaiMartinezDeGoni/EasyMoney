<?php
header('Content-Type: application/json');  // Todas las respuestas serán JSON

$client_id = "7gkz3aw164wmtbo5u1m1n4cihpy8de";
$client_secret = "o99k9rok7tlprgf3bmzkqkdtqw6tan";
function obtenerTokenTwitch() {
    global $client_id, $client_secret;

    // URL del endpoint de Twitch para obtener el token
    $url = "https://id.twitch.tv/oauth2/token";

    // Datos que se enviarán en la petición POST
    $post_fields = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'client_credentials'
    ];

    // Inicializar cURL
    $ch = curl_init();

    // Configurar cURL
    curl_setopt($ch, CURLOPT_URL, $url);  // URL del endpoint
    curl_setopt($ch, CURLOPT_POST, true);  // Petición POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));  // Datos POST
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Recibir respuesta como string

    // Ejecutar la petición y capturar la respuesta
    $response = curl_exec($ch);

    // Verificar si ocurrió algún error
    if ($response === false) {
    die('Error de cURL: ' . curl_error($ch));
    }

    // Cerrar la conexión cURL
    curl_close($ch);

    // Decodificar la respuesta JSON
    $data = json_decode($response, true);
    return $data['access_token'];
}

function verificarToken($access_token) {
    $url = "https://id.twitch.tv/oauth2/validate";

    $headers = [
        "Authorization: OAuth $access_token"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // Si el token es válido, devolvemos true
    return isset($data['client_id']);
}

/*function getValidAccessToken($access_token = null) {
    global $client_id, $client_secret;
    // Si no se proporciona un token o el token actual no es válido, obtenemos uno nuevo
    if (!$access_token || !verificarToken($access_token)) {
        $nuevo_token = obtenerNuevoToken($client_id, $client_secret);
        return $nuevo_token['access_token'];
    }

    // Si el token es válido, lo devolvemos
    return $access_token;
}*/

function getStreamerInfo($streamer_id, $access_token) {
    global $client_id;
    $url = "https://api.twitch.tv/helix/users?id=" . $streamer_id;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);

}

 function getStreamsInfo($access_token){
    global $client_id;
    $url = "https://api.twitch.tv/helix/streams";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
 }

?>