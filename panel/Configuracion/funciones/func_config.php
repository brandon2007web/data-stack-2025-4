<?php
session_start();
$message = "";

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: /IniciarSesion/iniciarsesion.php");
    exit();
}

// Variables de sesión, si el ID no existe, se inicializa a 0
$usuario_id = $_SESSION['usuario_id'] ?? 0;
$nombre     = $_SESSION['nombre'] ?? '';
$apellido   = $_SESSION['apellido'] ?? '';
$correo     = $_SESSION['correo'] ?? '';

include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_nuevo   = $_POST['nombre'] ?? '';
    $apellido_nuevo = $_POST['apellido'] ?? '';
    $correo_nuevo   = $_POST['correo'] ?? '';

    // 1. Validar si el ID de usuario es válido antes de proceder
    if ($usuario_id === 0) {
        $message = "❌ Error: ID de usuario no encontrado en la sesión. Inicie sesión nuevamente.";
    } else {
        // Actualizar registro existente
        $stmt_update = $conn->prepare(
            "UPDATE usuario SET Nombre = ?, Apellido = ?, Correo = ? WHERE ID_Usuario = ?"
        );

        if ($stmt_update) {
            // CORRECCIÓN CLAVE: Agregamos 'i' para indicar que ID_Usuario es un entero, 
            // y pasamos la variable $usuario_id al final.
            if ($stmt_update->bind_param("sssi", $nombre_nuevo, $apellido_nuevo, $correo_nuevo, $usuario_id)) {

                if ($stmt_update->execute()) { // <--- LA BD SE ACTUALIZA AQUÍ
                    $message = "✅ Datos actualizados correctamente.";

                    // Actualizar sesión y variables (SOLO DESPUÉS DE LA BD)
                    $_SESSION['nombre']   = $nombre_nuevo;
                    $_SESSION['apellido'] = $apellido_nuevo;
                    $_SESSION['correo']   = $correo_nuevo;

                    $nombre   = $nombre_nuevo;
                    $apellido = $apellido_nuevo;
                    $correo   = $correo_nuevo;
                } else {
                    $message = "❌ Error al actualizar: " . $stmt_update->error;
                }
            } else {
                 $message = "❌ Error al enlazar parámetros: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $message = "❌ Error en la preparación de la consulta: " . $conn->error;
        }
    }
}

$conn->close();
?>