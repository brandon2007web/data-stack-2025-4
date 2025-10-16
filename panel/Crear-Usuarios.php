<?php
session_start();

// Este archivo debería ser accesible solo por el Administrador (rol_id = 1)
// Si el usuario no está logueado o no es Admin, lo redirigimos.
if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? 0) != 1) {
    header("Location: /acceso_denegado.php"); // Asegúrate de tener una página de acceso denegado
    exit();
}

// Variables de sesión del Admin (para mostrar en el encabezado si se desea)
$nombre = $_SESSION['nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Crear Usuarios - Panel Admin</title>
    <style>
        :root{--bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;--brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
        .container{max-width:800px;margin:0 auto;padding:28px 16px}
        .card-grid{display:grid;grid-template-columns:1fr;gap:20px;margin-top:20px}
        @media (min-width: 600px) {.card-grid{grid-template-columns:1fr 1fr;}}
        .choice-card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:24px;text-align:center;box-shadow:0 4px 6px rgba(0,0,0,.05);transition:transform .3s, box-shadow .3s;}
        .choice-card:hover{transform:translateY(-5px);box-shadow:0 8px 15px rgba(0,0,0,.1);}
        .choice-card h3{color:var(--brand);margin-top:0;}
        .choice-card p{color:var(--muted);font-size:14px;}
        .btn-select{border:0;border-radius:12px;background:var(--brand);color:#fff;padding:10px 18px;font-weight:600;cursor:pointer;margin-top:15px;display:inline-block;text-decoration:none;}
        .btn-select:hover{background:var(--brand-2);}
        .icon{font-size:40px;margin-bottom:10px;color:var(--brand);}
        h1{text-align:center;color:var(--text);margin-bottom:10px;}
        .subtitle{text-align:center;color:var(--muted);margin-top:0;font-size:16px;}
        .back-link{display:block;margin-bottom:20px;text-align:center;color:var(--brand);text-decoration:none;font-weight:500;}
    </style>
</head>
<body>
    <div class="container">
        <a href="/Data_Stack-2025/panel/index.php" class="back-link">← Volver al Panel Admin</a>
        
        <h1>Seleccionar Tipo de Usuario a Crear</h1>
        <p class="subtitle">Elige el rol del nuevo usuario para continuar con el registro.</p>

        <div class="card-grid">
            
            <!-- Bloque 1: Administrador -->
            <div class="choice-card">
                <div class="icon">👑</div>
                <h3>Administrador</h3>
                <p>Usuarios con acceso total a la configuración del sistema, horarios y gestión de otros usuarios. (Rol ID: 1)</p>
                <!-- Aquí se enlazaría al formulario de creación con el rol preseleccionado (Ejemplo: rol=1) -->
                <a href="Crear-Usuarios/crear-administrador.php" class="btn-select">Crear Admin</a>
            </div>

            <!-- Bloque 2: Docente -->
            <div class="choice-card">
                <div class="icon">🧑‍🏫</div>
                <h3>Docente</h3>
                <p>Usuarios con permisos limitados a la visualización y edición de sus propios recursos y horarios. (Rol ID: 2)</p>
                <!-- Aquí se enlazaría al formulario de creación con el rol preseleccionado (Ejemplo: rol=2) -->
                <a href="Crear-Usuarios/crear-docente.php" class="btn-select">Crear Docente</a>
            </div>

        </div>
    </div>
</body>
</html>
