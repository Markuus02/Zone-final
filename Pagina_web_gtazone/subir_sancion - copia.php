<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "discord_db";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Variables del formulario
$pruebasBan = $_POST['pruebas_ban'] ?? '';
$archivosGuardados = [];

if (isset($_FILES['archivos_prueba'])) {
    $archivos = $_FILES['archivos_prueba'];
    $rutaDestino = "uploads/pruebas/";

    // Procesar cada archivo
    for ($i = 0; $i < count($archivos['name']); $i++) {
        $tmpName = $archivos['tmp_name'][$i];
        $nombreOriginal = basename($archivos['name'][$i]);

        // Evitar conflictos con nombres duplicados
        $nombreSeguro = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombreOriginal);
        $rutaFinal = $rutaDestino . $nombreSeguro;

        // Validación básica (opcional)
        $extension = strtolower(pathinfo($rutaFinal, PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        if (in_array($extension, $permitidos)) {
            if (move_uploaded_file($tmpName, $rutaFinal)) {
                $archivosGuardados[] = $rutaFinal;
            }
        }
    }
}

// Convertir a JSON para guardar en la base de datos
$archivoPruebaJson = json_encode($archivosGuardados, JSON_UNESCAPED_SLASHES);

// Insertar en la base de datos
$stmt = $conn->prepare("INSERT INTO sanciones (pruebas, archivo_prueba) VALUES (?, ?)");
$stmt->bind_param("ss", $pruebasBan, $archivoPruebaJson);

if ($stmt->execute()) {
    echo "✅ Sanción guardada correctamente.";
} else {
    echo "❌ Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
