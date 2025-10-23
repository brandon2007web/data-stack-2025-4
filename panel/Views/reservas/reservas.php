<?php
include(__DIR__.'/funciones/func_resera.php')
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Reserva</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Reserva</h1>

        <?php if ($message_text): ?>
            <div class="mensaje <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message_text); ?>
            </div>
        <?php endif; ?>

        <?php if ($id_usuario_actual === null): ?>
            <div class="mensaje error">
                ⚠️ Error: No se encontró el ID del usuario en la sesión. Por favor, inicie sesión antes de crear una reserva.
            </div>
        <?php else: ?>

        <form action="procesar_reserva.php" method="POST">

            <!-- ID del usuario en sesión -->
            <input type="hidden" name="ID_Usuario" value="<?php echo htmlspecialchars($id_usuario_actual); ?>">

            <label for="id_recurso">Recurso (Equipo):</label>
            <select id="id_recurso" name="ID_Recurso" required>
                <option value="">-- Seleccione un Recurso --</option>
                <?php foreach ($recursos_disponibles as $recurso): ?>
                    <option value="<?php echo htmlspecialchars($recurso['ID_Recurso']); ?>">
                        <?php echo htmlspecialchars($recurso['Nombre']) . " (Estado: " . htmlspecialchars($recurso['Estado']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_aulas">Aula / Sala:</label>
            <select id="id_aulas" name="ID_Aulas" required>
                <option value="">-- Seleccione un Aula --</option>
                <?php foreach ($aulas_disponibles as $aula): ?>
                    <option value="<?php echo htmlspecialchars($aula['ID_Aula']); ?>">
                        <?php echo htmlspecialchars($aula['Nombre']) . " (Piso: " . htmlspecialchars($aula['ID_Piso']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="inicio_reserva">Inicio de la Reserva:</label>
            <input type="datetime-local" id="inicio_reserva" name="Inicio_Reserva" required>

            <label for="fin_reserva">Fin de la Reserva:</label>
            <input type="datetime-local" id="fin_reserva" name="Fin_Reserva" required>

            <label for="proposito">Propósito / Motivo:</label>
            <textarea id="proposito" name="Descripcion_Motivo" rows="3" placeholder="Ejemplo: uso del proyector para clase de informática" required></textarea>

            <button type="submit">Confirmar Reserva</button>
        </form>

        <?php endif; ?>
    </div>
</body>
</html>
