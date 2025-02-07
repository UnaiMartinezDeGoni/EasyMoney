<?php
header('Content-Type: application/json'); 

$client_id = "7gkz3aw164wmtbo5u1m1n4cihpy8de";
$client_secret = "o99k9rok7tlprgf3bmzkqkdtqw6tan";

//Función para solicitar el access token
function obtenerTokenTwitch() {
  
    //Tomar las variables globales
    global $client_id, $client_secret;

    //URL del endpoint de Twitch para obtener el token
    $url = "https://id.twitch.tv/oauth2/token";

    //Datos que se enviarán en la petición POST
    $post_fields = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'client_credentials'
    ];

    $ch = curl_init();

    //Configurar Curl
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $respuesta = curl_exec($ch);

    curl_close($ch);

    //Decodificar la respuesta JSON
    $datos = json_decode($respuesta, true);
    return $datos['access_token'];
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

    $respuesta = curl_exec($ch);
    curl_close($ch);

    $datos = json_decode($respuesta, true);

    //Si el token es válido, devolvemos true
    return isset($datos['client_id']);
}

//Pedir información del streamer
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

    $respuesta = curl_exec($ch);
    curl_close($ch);

    return json_decode($respuesta, true);

}

//Obtener información de los streams
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

    $respuesta = curl_exec($ch);
    curl_close($ch);

    return json_decode($respuesta, true);
 }

?>