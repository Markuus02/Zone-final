<?php
function obtenerDiscordClientId() {
    $v = getenv('DISCORD_CLIENT_ID');
    if ($v && strlen($v) > 5) return $v;
    return '1377056842722836560'; // Valor por defecto
}
function obtenerDiscordClientSecret() {
    $v = getenv('DISCORD_CLIENT_SECRET');
    if ($v && strlen($v) > 10) return $v;
    return 'DwyPVjyNPOXYBsFoRZjJtGbmlffYRD6G'; // Valor por defecto
}
function obtenerDiscordRedirectUri() {
    $v = getenv('DISCORD_REDIRECT_URI');
    if ($v && strlen($v) > 10) return $v;
    return 'http://localhost/Pagina_web_gtazone/callback.php'; // Valor por defecto
}