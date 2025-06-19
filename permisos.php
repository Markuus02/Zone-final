<?php
require_once __DIR__ . '/vendor/autoload.php'; // Asegúrate de que la ruta es correcta

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración: roles y permisos
$rolesPermisos = [
    'buscar_sanciones' => ['1129159480068804668'],
    'editar_sanciones' => ['1129159480068804669'],
    'eliminar_sanciones' => ['1129159480068804668'],
];

// Obtiene el token desde variable de entorno (NO hay valor por defecto)
function obtenerBotToken() {
    $token = getenv('DISCORD_BOT_TOKEN');
    if ($token && strlen($token) > 10) {
        return $token;
    }
    // Si no hay token, error crítico
    error_log('No se ha definido la variable de entorno DISCORD_BOT_TOKEN');
    return null;
}

function obtenerGuildId() {
    return '1129141270435086458';
}

/**
 * Verifica si el usuario tiene el permiso solicitado
 * @param string $permiso Nombre del permiso a verificar
 * @return bool true si tiene permiso, false si no
 */
function tienePermiso(string $permiso): bool {
    global $rolesPermisos;

    if (!isset($_SESSION['discord_id'])) {
        error_log('No hay discord_id en sesión');
        return false;
    }

    if (!isset($rolesPermisos[$permiso])) {
        error_log('Permiso no definido: ' . $permiso);
        return false;
    }

    $guildId = obtenerGuildId();
    $botToken = obtenerBotToken();
    if (!$botToken) {
        error_log('No hay token de bot disponible');
        return false;
    }
    $requiredRoles = $rolesPermisos[$permiso];
    $discordId = $_SESSION['discord_id'];

    $url = "https://discord.com/api/guilds/$guildId/members/$discordId";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bot $botToken"]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log('Discord API error: HTTP ' . $httpCode . ' - URL: ' . $url);
        return false;
    }

    $data = json_decode($response, true);

    if (!isset($data['roles']) || !is_array($data['roles'])) {
        error_log('No roles en respuesta Discord: ' . print_r($data, true));
        return false;
    }

    foreach ($data['roles'] as $userRole) {
        if (in_array($userRole, $requiredRoles, true)) {
            return true;
        }
    }
    return false;
}
