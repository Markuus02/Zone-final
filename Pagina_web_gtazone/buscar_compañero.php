<?php
$bot_token = 'MTM3NzA1Njg0MjcyMjgzNjU2MA.GqgC1j._oWN6RCORV9Wkki4xDbj10zKFJsUbKU_0ktQd8'; // ← Reemplaza con el token de tu bot
$guild_id = '1129141270435086458';
$rol_permitido_id = '1129159480068804668'; // ← ID del rol que deben tener

header('Content-Type: application/json');

// Validación de búsqueda
$query = $_GET['q'] ?? '';
if (!$query) {
    echo json_encode([]);
    exit;
}

// Buscar miembros
$ch = curl_init("https://discord.com/api/guilds/$guild_id/members/search?query=" . urlencode($query) . "&limit=10");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bot $bot_token",
    "Content-Type: application/json"
]);
$res = curl_exec($ch);
curl_close($ch);
$members = json_decode($res, true);

// Validar respuesta
if (!is_array($members)) {
    echo json_encode([]);
    exit;
}

// Filtrar solo los que tengan el rol permitido
$filtrados = [];

foreach ($members as $m) {
    if (in_array($rol_permitido_id, $m['roles'] ?? [])) {
        $filtrados[] = [
            'id' => $m['user']['id'],
            'username' => $m['user']['username'],
            'avatar' => $m['user']['avatar'] ?? null
        ];
    }
}

echo json_encode($filtrados);
