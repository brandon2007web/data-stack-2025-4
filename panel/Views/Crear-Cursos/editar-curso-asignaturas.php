<?php
include(__DIR__ . '/php/func_editar-cursos.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar asignaturas de <?= $curso_nombre ?></title>
  <link rel="stylesheet" href="styles/editar-cursos.css">
</head>
<body>
  <div class="container">
    <h1>Editar Asignaturas del Curso: <?= $curso_nombre ?></h1>

    <?php if ($mensaje['text']): ?>
      <div class="message <?= $mensaje['type'] ?>"><?= $mensaje['text'] ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="asignaturas-list">
        <?php foreach ($asignaturas as $a): ?>
          <label>
            <input type="checkbox" name="asignaturas[]" value="<?= $a['ID_Asignatura'] ?>"
              <?= isset($asociadas[$a['ID_Asignatura']]) ? 'checked' : '' ?>>
            <?= htmlspecialchars($a['Nombre']) ?>
          </label>
        <?php endforeach; ?>
      </div>
      <button type="submit">💾 Guardar Cambios</button>
    </form>
    <a class="back-link" href="listar_Cursos.php">⬅ Volver</a>
  </div>
</body>
</html>
