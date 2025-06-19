<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['discord_staff_compañero'])) {
        $nombres_string = $_POST['discord_staff_compañero'];
        $nombres = explode(',', $nombres_string);
        $nombres = array_map('trim', $nombres);

        foreach ($nombres as &$nombre) {
            $nombre = preg_replace('/(?<!\d)0$/', '', $nombre);
        }
        unset($nombre);

        foreach ($nombres as $nombre) {
            echo "Nombre procesado: " . htmlspecialchars($nombre) . "<br>";
            // Aquí guardas en la base de datos o haces lo que necesites
        }
    } else {
        echo "No se recibió el campo discord_staff_compañero";
    }
} else {
    echo "Acceso no permitido";
}
?>
