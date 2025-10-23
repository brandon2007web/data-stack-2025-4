<?php
session_start();
require __DIR__ . "/php/procesar_crear_cursos.php"; // Procesa el formulario

// Capturar mensaje y bandera para mostrar GIF
$message_text = $_SESSION['message']['text'] ?? '';
$message_type = $_SESSION['message']['type'] ?? '';
$show_gif = $_SESSION['message']['show_gif'] ?? false;

// Limpiar sesión
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Nuevo Curso</title>
  <link rel="stylesheet" href="styles/dark-styles.css">
</head>
<body>
  <div class="container">
    <h1>Crear Nuevo Curso y Asignar Asignaturas</h1>

    <form method="POST" action="">
      <label for="nombre_curso">Nombre del Curso:</label>
      <input type="text" name="nombre_curso" id="nombre_curso" required maxlength="255">

      <label>Asignaturas del Curso:</label>
      <div class="asignaturas-list">
        <?php if (count($asignaturas) > 0): ?>
          <?php foreach ($asignaturas as $a): ?>
            <label>
              <input type="checkbox" name="asignaturas[]" value="<?= htmlspecialchars($a['ID_Asignatura']); ?>">
              <?= htmlspecialchars($a['Nombre']); ?>
            </label>
          <?php endforeach; ?>
        <?php else: ?>
          <p>⚠️ No hay asignaturas registradas.</p>
        <?php endif; ?>
      </div>

      <button type="submit">Crear Curso</button>
    </form>

    <a href="listar_Cursos.php" class="action-link">📋 Ver cursos</a>
    <a href="../../index.php" class="back-link">⬅ Volver al Panel</a>
  </div>

  <!-- Modal GIF -->
  <?php if($show_gif): ?>
  <div id="gifModal" style="display:flex;">
    <div id="gifModalContent">
      <img src="../../../gif/tick.gif" alt="Éxito">
      <p><?= htmlspecialchars($message_text); ?></p>
    </div>
  </div>
  <script>
    // Duración de la animación en milisegundos
    const duracion = 2000; // 2.5 segundos
    setTimeout(() => {
      document.getElementById('gifModal').style.display = 'none';
    }, duracion);
  </script>
  <?php endif; ?>

</body>
</html>
