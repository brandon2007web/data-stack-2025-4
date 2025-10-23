<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$nombre = $_SESSION['nombre'] ?? 'Invitado';
$rol_id = $_SESSION['rol'] ?? 0;
?>

<header>
    <div class="header-container">
        <!-- Logo -->
        <div class="logo">
            <a href="index.php">Data Stack Admin</a>
        </div>

        <!-- Menú de navegación -->
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="../bienvenido/bienvenido.php">Volver a la Página Principal</a></li>
            </ul>
        </nav>

        <!-- Controles de usuario -->
        <div class="user-controls">
            <div class="avatar"><?= strtoupper(substr($nombre, 0, 2)) ?></div>
            <span class="username"><?= htmlspecialchars($nombre) ?></span>
            <a href="cerrar-sesion.php" class="logout-btn">Cerrar sesión</a>
        </div>
    </div>
</header>

<style>
/* HEADER GENERAL */
header {
    position: fixed;
    top: 0;
    width: 100%;
    background-color: #131b24ff; /* azul profesional */
    color: white;
    padding: 10px 20px;
    box-shadow: 0 2px 8px rgba(49, 48, 48, 0.1);
    z-index: 1000;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: auto;
}

.logo a {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    text-decoration: none;
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
    border-radius: 5px;
    transition: background 0.3s, color 0.3s;
}

nav ul li a:hover {
    background-color: #182430ff;
}

.user-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-controls .avatar {
    background-color: #fff;
    color: #4A90E2;
    font-weight: 600;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-controls .username {
    font-weight: 500;
    color: white;
}

.logout-btn {
    background-color: #e74c3c;
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background 0.3s;
}

.logout-btn:hover {
    background-color: #c0392b;
}

/* Responsive pequeño */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 10px;
    }

    nav ul {
        flex-direction: column;
        gap: 5px;
    }
}
</style>
