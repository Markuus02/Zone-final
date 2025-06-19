<?php
session_start();
require_once 'config_discord.php';
$client_id = obtenerDiscordClientId();
$redirect_uri = urlencode(obtenerDiscordRedirectUri());
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login con Discord</title>
    <link rel="stylesheet" href="styles_index1.css"> <!-- Vinculamos CSS externo -->
</head>
<body>

<?php if (!isset($_SESSION['user'])): ?>
    <img src="log_gtazone.png" alt="Logo" class="logo"> 
    <h1>Iniciar sesión con Discord</h1>
    <a class="login-btn" href="https://discord.com/oauth2/authorize?client_id=<?=$client_id?>&redirect_uri=<?=$redirect_uri?>&response_type=code&scope=identify+guilds+guilds.members.read">
        Conectar con Discord
    </a>
<?php else: ?>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
    <a class="login-btn" href="logout.php">Cerrar sesión</a>
<?php endif; ?>

</body>
</html>
