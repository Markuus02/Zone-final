<?php
session_start();

if (!isset($_SESSION['user']) && isset($_COOKIE['discord_user'])) {
    $_SESSION['user'] = json_decode($_COOKIE['discord_user'], true);
}

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$host = 'localhost';
$db = 'discord_db';
$user_db = 'root';
$pass_db = '';
$conn = new mysqli($host, $user_db, $pass_db, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

if (isset($_GET['discord_usuario_id']) || isset($_GET['nombre_usuario']) || isset($_GET['hexa_usuario'])) {

    if (isset($_GET['discord_usuario_id'])) {
        $discord_id = $conn->real_escape_string($_GET['discord_usuario_id']);
        $sql = "SELECT nombre_usuario, hexa_usuario FROM sanciones WHERE discord_usuario_id = '$discord_id' ORDER BY id DESC LIMIT 1";
    } elseif (isset($_GET['nombre_usuario'])) {
        $nombre = $conn->real_escape_string($_GET['nombre_usuario']);
        $sql = "SELECT nombre_usuario, hexa_usuario, discord_usuario_id FROM sanciones WHERE nombre_usuario = '$nombre' ORDER BY id DESC LIMIT 1";
    }
    elseif (isset($_GET['hexa_usuario'])) {
        $hexa_usuario = $conn->real_escape_string($_GET['hexa_usuario']);
        $sql = "SELECT nombre_usuario, hexa_usuario, discord_usuario_id FROM sanciones WHERE hexa_usuario = '$hexa_usuario' ORDER BY id DESC LIMIT 1";
    }

    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => $conn->error, 'sql' => $sql]);
        exit;
    }

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]); // No encontrado
    }
    exit;
}

// buscar_sanciones.php
header('Content-Type: application/json');
echo json_encode($resultados); // incluso si $resultados = [];

http_response_code(400);
echo json_encode(['error' => 'Parámetros faltantes']);

?>
