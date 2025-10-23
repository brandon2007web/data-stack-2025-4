<?php
session_start();

// Acceso solo para Admin
if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? 0) != 1) {
    header("Location: /acceso_denegado.php");
    exit();
}

$nombre = $_SESSION['nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Crear Usuarios - Panel Admin</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
    --bg: #f4f6fc;
    --card: #fff;
    --text: #1e1e2f;
    --muted: #6c6f85;
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --line: #e5e7eb;
    --radius: 16px;
}

* { box-sizing: border-box; margin:0; padding:0; }

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    color: var(--text);
}

.container {
    max-width: 900px;
    margin: 100px auto 50px;
    padding: 0 1rem;
}

.back-link {
    display: inline-block;
    margin-bottom: 25px;
    color: var(--primary);
    font-weight: 500;
    text-decoration: none;
    transition: color 0.3s;
}
.back-link:hover { color: var(--primary-dark); }

h1 {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.subtitle {
    text-align: center;
    color: var(--muted);
    margin-bottom: 40px;
    font-size: 1.05rem;
}

.card-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 25px;
}

@media(min-width: 600px) {
    .card-grid { grid-template-columns: 1fr 1fr; }
}

.choice-card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 30px 20px;
    text-align: center;
    border: 1px solid var(--line);
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.choice-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.12);
}

.choice-card .icon {
    font-size: 50px;
    margin-bottom: 15px;
    color: var(--primary);
    transition: transform 0.3s ease;
}
.choice-card:hover .icon { transform: scale(1.15); }

.choice-card h3 {
    color: var(--primary-dark);
    margin-bottom: 10px;
    font-size: 1.4rem;
}

.choice-card p {
    color: var(--muted);
    font-size: 0.95rem;
    line-height: 1.4;
    margin-bottom: 20px;
}

.btn-select {
    display: inline-block;
    padding: 12px 22px;
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    transition: background 0.3s, transform 0.2s;
}
.btn-select:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
}
</style>
</head>
<body>
<div class="container">
    <a href="/Data_Stack-2025/panel/index.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Panel Admin</a>
    
    <h1>Seleccionar Tipo de Usuario a Crear</h1>
    <p class="subtitle">Elige el rol del nuevo usuario para continuar con el registro.</p>

    <div class="card-grid">
        <!-- Administrador -->
        <div class="choice-card">
            <div class="icon"><i class="fas fa-crown"></i></div>
            <h3>Administrador</h3>
            <p>Usuarios con acceso total a la configuración del sistema, gestión de horarios y otros usuarios. (Rol ID: 1)</p>
            <a href="Crear-Usuarios/crear-administrador.php" class="btn-select">Crear Admin</a>
        </div>

        <!-- Docente -->
        <div class="choice-card">
            <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <h3>Docente</h3>
            <p>Usuarios con permisos limitados a la visualización y edición de sus propios recursos y horarios. (Rol ID: 2)</p>
            <a href="Crear-Usuarios/crear-docente.php" class="btn-select">Crear Docente</a>
        </div>
    </div>
</div>
</body>
</html>
