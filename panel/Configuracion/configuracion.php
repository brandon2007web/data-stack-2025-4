<?php
include(__DIR__.'/funciones/func_config.php')
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Configuración de Usuario</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>⚙️ Configuración de Usuario</h2>

    <?php if ($message): ?>
        <div class="message <?= str_contains($message, '✅') ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>">

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($apellido) ?>">

        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($correo) ?>">

        <button type="submit">💾 Guardar Cambios</button>
    </form>

    <a href="../../bienvenido/bienvenido.php" class="back-link">⬅️ Volver al Inicio</a>
</div>

</body>
</html>
