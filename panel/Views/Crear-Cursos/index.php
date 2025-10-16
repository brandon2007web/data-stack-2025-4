<?php
// index.php - Panel de administrador

// Iniciar sesión si tu panel necesita autenticación
session_start();
include("../conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Tu CSS -->
</head>
<body>

    <!-- Incluir header -->
    <?php include("views/header.php"); ?>

    <main>
        <div class="container">
            <h1>Bienvenido al Panel de Administrador</h1>
            <p>Desde aquí podrás gestionar tu sistema.</p>

            <!-- Ejemplo de sección de administración -->
            <div class="dashboard-sections">
                <a href="crear-Cursos.php" class="dashboard-card">Cursos</a>
                <a href="views/Crear-Grupos/grupos.php" class="dashboard-card">Grupos</a>
                <a href="Crear-Usuarios.php" class="dashboard-card">Usuarios</a>
                <a href="views/aulas/aulas.php" class="dashboard-card">aulas</a>
                <a href="views/horarios/horarios.php"class="dashboard-card">Horarios</a>
            </div>
        </div>
    </main>

    <!-- Incluir footer -->
    <?php include("views/footer.php"); ?>

</body>
</html>
