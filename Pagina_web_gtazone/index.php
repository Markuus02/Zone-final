<?php
session_start();
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
    <a class="login-btn" href="https://discord.com/oauth2/authorize?client_id=1377056842722836560&redirect_uri=http%3A%2F%2Flocalhost%2FPagina_web_gtazone%2Fcallback.php&response_type=code&scope=identify+guilds+guilds.members.read">
        Conectar con Discord
    </a>
<?php else: ?>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
    <a class="login-btn" href="logout.php">Cerrar sesión</a>
<?php endif; ?>

</body>
</html>
