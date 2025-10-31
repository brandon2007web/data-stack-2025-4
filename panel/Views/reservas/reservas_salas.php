<?php 
session_start();

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    header("Location: /ruta/al/login.php");
    exit;
}

// Solo permitir acceso a roles 1 (admin) o 2 (gestor)
if (!in_array($_SESSION['rol'], [1, 2])) {
    // O podés mostrar un mensaje o página de acceso denegado
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>🚫 Acceso denegado</h2>";
    exit;
}



include(__DIR__."/../../../conexion.php");

$view = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$validViews = [
    'dashboard'         => 'views/dashboard.php',
    'lista-salas'       => 'views/lista_salas.php',
    'reservar-sala'     => 'Views/reservar_salas.php',
    'reporte-reservas'  => 'Views/reporte_reservas.php',
    'configuraciones'   => 'Views/configuraciones.php'
];

function active($page, $view) {
    return $page === $view ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Reservas</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/lista_salas.css">
    <link rel="stylesheet" href="assets/css/reservar_salas.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4>Sistema SRS</h4>
        <a class="<?= active('dashboard', $view) ?>" href="reservas_salas.php?page=dashboard">Dashboard</a>
        <a class="<?= active('lista-salas', $view) ?>" href="reservas_salas.php?page=lista-salas">Lista de Salas</a>
        <a class="<?= active('reservar-sala', $view) ?>" href="reservas_salas.php?page=reservar-sala">Reservar Sala</a>
        <hr>
        <a href="/data-stack-2025-4/bienvenido/vista_portal.php" class="text-danger">Volver al Inicio</a>
    </div>

    <!-- MAIN AREA -->
    <div class="main-area">

        <!-- HEADER -->
        <div class="top-header">
            <span>Bienvenido</span>
            <span><?php echo $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></span>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="main-content">
            <?php
                if (array_key_exists($view, $validViews)) {
                    include $validViews[$view];
                } else {
                    echo "<h3>Página no encontrada</h3>";
                }
            ?>
        </div>
    </div>

</div>

<!-- SCRIPTS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
