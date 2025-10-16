<?php
// header.php
// Iniciar sesión si es necesario
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Aquí podrías poner verificación de usuario/admin
// if (!isset($_SESSION['usuario'])) {
//     header("Location: login.php");
//     exit();
// }
?>

<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php"> Panel Admin</a>
        </div>
        <nav>
            <ul>
                <li><a href="http://localhost/Data_Stack-2025-2/panel/index.php">Inicio</a></li>
                <li><a href="../bienvenido/bienvenido.php">Volver pagina Principal</a></li>
            </ul>
        </nav>
    </div>
</header>

<style>
/* Estilo básico para el header */
header {
    position: fixed;
    background-color: #2c3e50;
    padding: 15px 20px;
    color: white;
    margin:0;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-container .logo a {
    color: white;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: bold;
}

nav ul {
    list-style: none;
    display: flex;
    gap: 15px;
    margin: 0;
    padding: 0;
}

nav ul li a {
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    transition: background 0.3s, color 0.3s;
}

nav ul li a:hover {
    background-color: #34495e;
    border-radius: 5px;
}
</style>
