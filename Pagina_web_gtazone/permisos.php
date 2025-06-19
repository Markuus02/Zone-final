<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración: roles y permisos
$rolesPermisos = [
    'buscar_sanciones' => ['1129159480068804668'],
    'editar_sanciones' => ['1129159480068804669'],
    'eliminar_sanciones' => ['1129159480068804668'],
];

// Obtiene el token desde variable de entorno o usa el nuevo valor por defecto
function obtenerBotToken() {
    $token = getenv('DISCORD_BOT_TOKEN');
    if ($token && strlen($token) > 10) {
        return $token;
    }
    // Valor por defecto (nuevo token)
    return 'MTM3NzA1Njg0MjcyMjgzNjU2MA.GwZGaq.Py3wlYcvla7JucI0ZdcM4EbAabOg5KPzOljCAQ';
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
        return false;
    }

    if (!isset($rolesPermisos[$permiso])) {
        return false;
    }

    $guildId = obtenerGuildId();
    $botToken = obtenerBotToken();
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
        return false;
    }

    $data = json_decode($response, true);

    if (!isset($data['roles']) || !is_array($data['roles'])) {
        return false;
    }

    foreach ($data['roles'] as $userRole) {
        if (in_array($userRole, $requiredRoles, true)) {
            return true;
        }
    }
    return false;
}
