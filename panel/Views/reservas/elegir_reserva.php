<?php
session_start();

// Acceso solo para Admin o Docente (por ejemplo)
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'] ?? 0, [1, 2])) {
    header("Location: /acceso_denegado.php");
    exit();
}

$nombre = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Crear Reservas - Panel</title>

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
    <a href="/Data_Stack-2025/panel/index.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    
    <h1>Seleccionar Tipo de Reserva</h1>
    <p class="subtitle">Elige el tipo de reserva que deseas realizar.</p>

    <div class="card-grid">
        <!-- Reserva de Aula -->
        <div class="choice-card">
            <div class="icon"><i class="fas fa-school"></i></div>
            <h3>Reserva de Aula</h3>
            <p>Permite reservar un aula específica para una clase, reunión o actividad. Se puede definir fecha, hora y duración.</p>
            <a href="Reservas/crear-reserva-aula.php" class="btn-select">Reservar Aula</a>
        </div>

        <!-- Reserva de Recurso -->
        <div class="choice-card">
            <div class="icon"><i class="fas fa-laptop"></i></div>
            <h3>Reserva de Recurso</h3>
            <p>Permite reservar equipos o materiales como proyectores, computadoras o cámaras, según disponibilidad.</p>
            <a href="crear-reserva-recursos.php" class="btn-select">Reservar Recurso</a>
        </div>
    </div>
</div>
</body>
</html>