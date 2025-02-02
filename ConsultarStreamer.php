<?php
$client_id = "ljl45mpyrvonphg5euw7ieiy6c1mc5";
$access_token = "ffxt352y68b5zr62e4yu0wv9lvpllo";  // Usa un token reciente
$user_id = "44322889"; // ID de usuario de Twitch

$url = "https://api.twitch.tv/helix/users?id=" . $user_id;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Client-ID: $client_id",
    "Authorization: Bearer $access_token"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Imprimir el JSON formateado
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
?>
