<?php
$host = '127.0.0.1';       // Dirección del servidor MySQL (localhost)
$usuario = 'root';         // Usuario de MySQL (por defecto es 'root')
$contrasena = '';          // Contraseña (en XAMPP o Laragon suele estar vacía)
$base_de_datos = 'discord_db';  // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
