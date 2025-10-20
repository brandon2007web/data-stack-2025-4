<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Portal estilo CREA 2 — Portada + Foro</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Sidebar solo para admin -->
  <?php if ($rol_id == 1): ?>
    <div class="hamburger" id="hamburger" style="position:absolute;top:20px;left:20px;z-index:1100">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <div class="sidebar" id="sidebar">
      <br><br>
      <h3>Menú Admin</h3>
      <a href="../panel/index.php" style="color:blue;">Panel Admin</a>
      <a href="../panel/Configuracion/configuracion.php">⚙️ Configuración</a>
    </div>
    <div class="overlay" id="overlay"></div>
  <?php endif; ?>

  <!-- Header -->
  <header>
    <div class="container header-inner">
      <div class="brand">
        <div class="logo">D.S</div>
        <div>
          <h1>Data Stack</h1>
          <p>ITSP</p>
        </div>
      </div>
      <nav class="main-nav">
        <a href="../panel/Views/horarios/ver_horarios.php" class="nav-link">Ver Horarios</a>
      </nav>
      <div class="user-controls">
        <div class="avatar"><?= strtoupper(substr($nombre, 0, 2)); ?></div>
        <span><?= htmlspecialchars($nombre); ?></span>
        <a href="cerrar-sesion.php" class="btn" style="margin-left:10px;">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div id="hero-bg" class="hero-bg"></div>
    <div class="hero-grad"></div>
    <div class="container hero-content" style="display:flex;flex-direction:column;justify-content:end;">
      <div class="hero-card">
        <h2 class="hero-title">Bienvenido/a <?= htmlspecialchars($nombre); ?></h2>
        <p class="hero-sub">Recursos, anuncios y espacio de intercambio para la comunidad.</p>
        <div id="dots" class="dots"></div>
      </div>
    </div>
  </section>

  <!-- Foro -->
  <main class="container">
    <h3 style="margin:0;font-size:20px;margin-bottom:10px">Foro</h3>
    <div class="card">
      <form id="post-form">
        <textarea id="contenido" placeholder="Escribe tu mensaje para el foro..."></textarea>
        <div class="form-footer">
          <p class="form-tip">Consejo: sé respetuoso y claro. Usa Shift+Enter para salto de línea.</p>
          <button class="btn" type="submit">Publicar</button>
        </div>
      </form>
    </div>
    <section id="lista-posts" style="margin-top:16px"></section>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container footer-inner">
      <p>© <span id="anio"></span> Portal estilo CREA 2</p>
      <p>Demostración sin backend</p>
    </div>
  </footer>

  <!-- Variables PHP a JS -->
  <script>
    const autorNombre = <?= json_encode($nombre); ?>;
    const esAdmin = <?= $rol_id == 1 ? 'true' : 'false'; ?>;
  </script>

  <script src="script.js"></script>
</body>
</html>
