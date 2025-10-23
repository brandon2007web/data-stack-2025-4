<?php
session_start();
include("../conexion.php");
$queryCursos = mysqli_query($conn, "SELECT COUNT(*) AS total FROM curso");
$totalCursos = mysqli_fetch_assoc($queryCursos)['total'] ?? 0;

// Contar grupos
$queryGrupos = mysqli_query($conn, "SELECT COUNT(*) AS total FROM grupo");
$totalGrupos = mysqli_fetch_assoc($queryGrupos)['total'] ?? 0;

// Contar usuarios
$queryUsuarios = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario");
$totalUsuarios = mysqli_fetch_assoc($queryUsuarios)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="estilos.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <!-- HEADER -->
    <?php include("views/header.php"); ?>

    <main>
        <h1>Panel de Administración</h1>
        <p class="subtitle">Gestiona todos los recursos del sistema desde un solo lugar</p>

        <!-- Mini Dashboard -->
  <div class="stats">
    <div class="stat-card">
        <i class="fas fa-book-open"></i>
        <div class="stat-info">
            <h3><?php echo $totalCursos; ?></h3>
            <p>Cursos activos</p>
        </div>
    </div>

    <div class="stat-card">
        <i class="fas fa-user-friends"></i>
        <div class="stat-info">
            <h3><?php echo $totalGrupos; ?></h3>
            <p>Grupos registrados</p>
        </div>
    </div>

    <div class="stat-card">
        <i class="fas fa-user-shield"></i>
        <div class="stat-info">
            <h3><?php echo $totalUsuarios; ?></h3>
            <p>Usuarios</p>
        </div>
    </div>
</div>

        <!-- Secciones principales -->
        <div class="dashboard-sections">
            <a href="Views/Crear-Cursos/crear-Cursos.php" class="dashboard-card">
                <i class="fas fa-book"></i>
                <span>Cursos</span>
            </a>
            <a href="Views/Crear-Grupos/grupos.php" class="dashboard-card">
                <i class="fas fa-users"></i>
                <span>Grupos</span>
            </a>
            <a href="Crear-Usuarios.php" class="dashboard-card">
                <i class="fas fa-user-cog"></i>
                <span>Usuarios</span>
            </a>
            <a href="Views/aulas/aulas.php" class="dashboard-card">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Aulas</span>
            </a>
            <a href="Views/horarios/horarios.php" class="dashboard-card">
                <i class="fas fa-calendar-alt"></i>
                <span>Horarios</span>
            </a>
            <a href="Views/recursos/crearrecursos.php" class="dashboard-card">
                <i class="fas fa-boxes-stacked"></i>
                <span>Recursos</span>
            </a>
            <a href="Views/reservas/reservas.php" class="dashboard-card">
                <i class="fas fa-calendar-check"></i>
                <span>Reservas</span>
            </a>
        </div>
    </main>

    <!-- FOOTER -->
    <?php include("views/footer.php"); ?>
</body>
</html>
