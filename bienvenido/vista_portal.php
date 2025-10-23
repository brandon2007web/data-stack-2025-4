<?php
include(__DIR__ . '/comprobacion.php');
include(__DIR__ . '/../conexion.php');

$nombre = $_SESSION['nombre'] ?? 'Invitado';
$rol_id = $_SESSION['rol'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>OrganizaT</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>

  <!-- HEADER -->
  <header class="site-header">
    <div class="container header-inner">
      <?php if ($rol_id == 1): ?>
      <button class="hamburger" id="hamburger" aria-label="Abrir menú de administrador">
        <span></span><span></span><span></span>
      </button>
      <?php endif; ?>
      <div class="brand">
        <div class="logo">D.S</div>
        <div class="brand-text">
          <h1>Data Stack</h1>
          <p>Instituto Tecnológico Superior Paysandú</p>
        </div>
      </div>
      <nav class="main-nav">
        <a href="../panel/Views/horarios/ver_horarios.php" class="nav-link">Ver Horarios</a>
      </nav>
      <div class="user-controls">
        <div class="avatar"><?= strtoupper(substr($nombre,0,2)) ?></div>
        <span class="username"><?= htmlspecialchars($nombre) ?></span>
        <button id="theme-toggle" class="btn ghost small" aria-pressed="false">🌓</button>
        <a href="cerrar-sesion.php" class="btn logout-btn">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <!-- SIDEBAR ADMIN -->
  <?php if ($rol_id == 1): ?>
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header"><h3>Menú Admin</h3></div>
    <nav class="sidebar-nav">
      <a href="../panel/index.php" class="sidebar-link">📊 Panel Admin</a>
      <a href="../panel/Configuracion/configuracion.php" class="sidebar-link">⚙️ Configuración</a>
    </nav>
  </aside>
  <div class="overlay" id="overlay"></div>
  <?php endif; ?>

  <!-- HERO -->
  <section class="hero">
    <div id="hero-bg" class="hero-bg"></div>
    <div class="hero-grad"></div>
    <div class="container hero-content">
      <div class="hero-card">
        <h2 class="hero-title">Bienvenido/a <?= htmlspecialchars($nombre) ?></h2>
        <p class="hero-sub">Explora recursos, anuncios y participa en la comunidad educativa.</p>
        <div id="dots" class="dots"></div>
      </div>
    </div>
  </section>

  <!-- INFO INSTITUCIONAL -->
  <section class="info-institucion">
    <div class="container">
      <h2>Sobre el Instituto Tecnológico Superior Paysandú</h2>
      <p>Somos una institución comprometida con la educación tecnológica de calidad, brindando herramientas y recursos para el desarrollo académico y profesional de nuestros estudiantes.</p>
      <p>Nuestra misión es formar profesionales capaces, innovadores y responsables, fomentando el aprendizaje continuo y la excelencia académica.</p>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="container footer-inner">
      <p>© <span id="anio"></span> Proyecto Data Stack -- OrganizaT</p>
      <p class="footer-sub">2025</p>
    </div>
  </footer>
  <script src="script.js"></script>
</body>
</html>
