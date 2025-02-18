<?php
require_once 'config.php'; // Asegura la conexión a la base de datos

global $mysqli;

function verificarAutenticacion() {
  echo "<pre>";
  print_r(getallheaders());  // Muestra todos los encabezados
  echo "</pre>";
  if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
      echo "Error: No Authorization header found.";  // Añadir para depuración
      http_response_code(401);
      echo json_encode(["error" => "Unauthorized. Token is required."]);
      exit;
  }

  $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
  echo "Authorization header: " . $authHeader . "<br>";  // Mostrar encabezado para depuración

  if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      echo "Error: Invalid token format. Header received: " . $authHeader . "<br>";  // Depuración
      http_response_code(401);
      echo json_encode(["error" => "Unauthorized. Invalid token format."]);
      exit;
  }

  $token = $matches[1]; // Extraer solo el token
  echo "Token extracted: " . $token . "<br>";

  // Verificar si el token está en la base de datos y sigue siendo válido
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT 1 FROM sessions WHERE token = ? AND expires_at > NOW()");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $result = $stmt->get_result();

  // Si el token no existe o está expirado
  if ($result->num_rows === 0) {
      http_response_code(401);
      echo "Error: Token is invalid or expired. Token checked: " . $token;
      echo json_encode(["error" => "Unauthorized. Token is invalid or expired."]);
      exit;
  }

  echo "Token is valid: " . $token; // Token válido, permite continuar



    return true; // El token es válido, permite continuar con la ejecución del código
}
?>
