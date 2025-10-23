<?php
// Inicia sesión solo si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión de usuario
if (!isset($_SESSION['usuario'])) {
    header("Location: /IniciarSesion/iniciarsesion.php");
    exit();
}

// Variables de sesión
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$rol = $_SESSION['rol'] ?? 0;
$rol_nombre = $_SESSION['rol_nombre'] ?? 'Desconocido';
?>
