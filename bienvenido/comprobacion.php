<?php
session_start();
// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: /IniciarSesion/iniciarsesion.php");
    exit();
}
// Variables de sesión
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$rol_id = $_SESSION['rol'] ?? 0;
$rol_nombre = $_SESSION['rol_nombre'] ?? 'Desconocido';
?>