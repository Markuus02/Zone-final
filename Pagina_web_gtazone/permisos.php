<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración: roles y permisos
// Cada permiso tiene asignados IDs de roles de Discord que lo tienen
$rolesPermisos = [
    'buscar_sanciones' => ['1129159480068804668'],
    'editar_sanciones' => ['1129159480068804669'],  // mismo ID para editar
    'eliminar_sanciones' => ['1129159480068804668'], // mismo ID para eliminar
];

function obtenerBotToken() {
    // Mejor usar variable de entorno o archivo config fuera webroot
    return 'MTM3NzA1Njg0MjcyMjgzNjU2MA.GqgC1j._oWN6RCORV9Wkki4xDbj10zKFJsUbKU_0ktQd8';
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
        return false; // No logueado
    }

    if (!isset($rolesPermisos[$permiso])) {
        return false; // Permiso no definido
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

    // Comprobar intersección roles del usuario con roles permitidos para el permiso
    foreach ($data['roles'] as $userRole) {
        if (in_array($userRole, $requiredRoles, true)) {
            return true;
        }
    }
    return false;
}
