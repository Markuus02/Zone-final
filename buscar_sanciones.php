<?php
session_start();
header('Content-Type: application/json');

require_once 'permisos.php';

// Verificamos si el usuario tiene permiso para buscar sanciones
if (!tienePermiso('buscar_sanciones')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '⛔ No tienes permiso para buscar sanciones.']);
    exit;
}

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=localhost;dbname=discord_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos."]);
    exit;
}

// Recibir datos enviados en JSON
$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre_usuario'] ?? '';
$discord = $data['discord_usuario_id'] ?? '';
$hexa = $data['hexa_usuario'] ?? '';

// Armar consulta SQL según los filtros
$query = "SELECT * FROM sanciones WHERE 1=1";
$params = [];

if (!empty($nombre)) {
    $query .= " AND nombre_usuario LIKE :nombre";
    $params[':nombre'] = "%$nombre%";
}
if (!empty($discord)) {
    $query .= " AND discord_usuario_id LIKE :discord";
    $params[':discord'] = "%$discord%";
}
if (!empty($hexa)) {
    $query .= " AND hexa_usuario LIKE :hexa";
    $params[':hexa'] = "%$hexa%";
}

$query .= " ORDER BY id DESC";

// Ejecutar consulta
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sanciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta", "detalles" => $e->getMessage()]);
    exit;
}

// Roles permitidos para editar sanciones (puedes ajustar esto en permisos.php si quieres)
$allowed_roles_editar = ['1129159480068804668'];

// Obtener roles del usuario con función tienePermiso (o guardarlos en sesión)
$discordRoles = obtenerRolesUsuario($_SESSION['discord_id']); // función que puedes crear para obtener roles (ver abajo)

// Verificar permiso de edición
$puedeEditar = count(array_intersect($discordRoles, $allowed_roles_editar)) > 0;

// Respuesta con sanciones y permiso de edición
echo json_encode([
    'success' => true,
    'sanciones' => $sanciones,
    'puede_editar' => $puedeEditar
]);

// --- Función para obtener roles del usuario ---
function obtenerRolesUsuario($userId) {
    $botToken = obtenerBotToken(); // Usar función de permisos.php
    $guildId = '1129141270435086458';
    $url = "https://discord.com/api/guilds/$guildId/members/$userId";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bot $botToken"]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['roles'] ?? [];
}
