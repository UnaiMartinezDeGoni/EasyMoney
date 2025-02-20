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

// Función para generar una API Key única
function generateApiKey() {
    return bin2hex(random_bytes(8)); // Genera una clave de 16 caracteres
}

// Función para generar un token para la API que sea único
function generateApiToken() {
    $token = bin2hex(random_bytes(8)); // Genera un token aleatorio de 16 caracteres
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 days')); // Se le asigna una fecha de expiración de 3 días

    return [
        'token' => $token,
        'expires_at' => $expires_at
    ];  // Devuelve el token
}

function obtenerTopVideosTwitch($mysqli, $access_token, $since = 600) {
    global $client_id; // Accede a la variable global

    $last_update_limit = date('Y-m-d H:i:s', time() - $since);
    $topGames = [];

    // Verificar si hay datos recientes en la caché de la base de datos
    $stmt = $mysqli->prepare("SELECT * FROM top_videos WHERE updated_at > ? ORDER BY most_viewed_views DESC LIMIT 120");
    $stmt->bind_param("s", $last_update_limit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $topGames[$row['game_id']][] = $row;
    }
    $stmt->close();

    // Si hay datos recientes en la base de datos, devuelve esos mismos datos
    if (count($topGames) >= 3) {
        return $topGames;
    }

    // Se conecta a la API de Twitch para obtener los 3 juegos más populares
    $top_games_url = "https://api.twitch.tv/helix/games/top?first=3";
    $ch = curl_init();  //Prepara la peticion de tipo POST para la API
    curl_setopt($ch, CURLOPT_URL, $top_games_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ]);
    $games_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // Para recorrer y tratar todos los datos obtenidos
    foreach ($games_response['data'] as $game) {
        $game_id = $game['id'];
        $game_name = $game['name'];

        // Verificar si 'game_id' ya está en 'top_games'
        $stmt = $mysqli->prepare("SELECT game_id FROM top_games WHERE game_id = ?");
        $stmt->bind_param("s", $game_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingGame = $result->fetch_assoc();
        $stmt->close();

        if (!$existingGame) {
            // Insertar el juego en 'top_games' si no existe
            $stmt = $mysqli->prepare("INSERT INTO top_games (game_id, game_name, updated_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $game_id, $game_name);
            if (!$stmt->execute()) {
                return ["error" => "Error al insertar en top_games", "detalle" => $stmt->error];
            }
            $stmt->close();
        }

        // Se conecta a la API de Twitch para obtener los 40 videos más vistos del juego
        $videos_url = "https://api.twitch.tv/helix/videos?game_id=$game_id&sort=views&first=40";
        $ch = curl_init();  //Prepara la peticion de tipo POST para la API
        curl_setopt($ch, CURLOPT_URL, $videos_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Client-ID: $client_id",
            "Authorization: Bearer $access_token"
        ]);
        $videos_response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // Estructura que utilizaremos para organizar los videos agrupados por streamer
        $videos_by_user = [];

        foreach ($videos_response['data'] as $video) {
            $user = $video['user_name'];

            // En el caso de que el usuario no esté en la lista, lo inicializamos con sus datos
            if (!isset($videos_by_user[$user])) {
                $videos_by_user[$user] = [
                    'game_id' => $game_id,
                    'game_name' => $game_name,
                    'user_name' => $user,
                    'total_videos' => 0,
                    'total_views' => 0,
                    'most_viewed_title' => $video['title'],
                    'most_viewed_views' => $video['view_count'],
                    'most_viewed_duration' => $video['duration'],
                    'most_viewed_created_at' => $video['created_at']
                ];
            }

            // Sumamos 1 al contador de videos del usuario y agrupamos el total de visitas
            $videos_by_user[$user]['total_videos']++;
            $videos_by_user[$user]['total_views'] += $video['view_count'];

            // En el caso de encontra un video con más visitas, actualizamos los datos del más visto
            if ($video['view_count'] > $videos_by_user[$user]['most_viewed_views']) {
                $videos_by_user[$user]['most_viewed_title'] = $video['title'];
                $videos_by_user[$user]['most_viewed_views'] = $video['view_count'];
                $videos_by_user[$user]['most_viewed_duration'] = $video['duration'];
                $videos_by_user[$user]['most_viewed_created_at'] = $video['created_at'];
            }
        }

        // Dependiendo del caso se insertaran o actualizaran los datos en la base de datos
        foreach ($videos_by_user as $video) {
            $stmt = $mysqli->prepare("INSERT INTO top_videos (game_id, user_name, total_videos, total_views, most_viewed_title, most_viewed_views, most_viewed_duration, most_viewed_created_at, updated_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW()) 
                                      ON DUPLICATE KEY UPDATE total_videos=?, total_views=?, most_viewed_title=?, most_viewed_views=?, most_viewed_duration=?, most_viewed_created_at=?, updated_at=NOW()");

            if (!$stmt) {
                return ["error" => "Error en la preparación de la consulta SQL"];
            }

            // Aqui se asignan los valores a la consulta para insertarlos o actualizarlos si ya existen en la base
            $stmt->bind_param("ssiisissississ", 
                $video['game_id'], $video['user_name'], $video['total_videos'], $video['total_views'], 
                $video['most_viewed_title'], $video['most_viewed_views'], $video['most_viewed_duration'], $video['most_viewed_created_at'],
                $video['total_videos'], $video['total_views'], $video['most_viewed_title'], $video['most_viewed_views'], 
                $video['most_viewed_duration'], $video['most_viewed_created_at']);

            // Se ejecuta la consulta comprobando que en su ejecuión no ocurra ningun error
            if (!$stmt->execute()) {
                return ["error" => "Error al insertar en la base de datos", "detalle" => $stmt->error];
            }

            $stmt->close(); // Se cierra la consulta con el fin de liberar memoria
            // Guardamos la información en la lista de los mejores videos por juego
            $topGames[$video['game_id']][] = $video;
        }
    }

    // Se devuelve la lista de los mejores videos organizados por juego
    return $topGames;
}
?>