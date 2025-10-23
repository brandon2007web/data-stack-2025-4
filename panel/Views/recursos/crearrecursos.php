<?php
include(__DIR__.'/funciones/func_recursos.php')
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Recurso</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Registrar Nuevo Recurso</h1>

        <?php
        // Mostrar mensaje de Sesión (ÉXITO o ERROR de inserción)
        if ($message_text): 
        ?>
            <div class="mensaje <?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php 
        endif; 

        // Mostrar error de carga de consultas (si tablas no existen, etc.)
        if ($error_carga) {
            echo '<div class="mensaje error">' . $error_carga . '</div>';
        }
        ?>

        <form action="procesar_creacion.php" method="POST">
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="Nombre" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="Descripcion" rows="3"></textarea>

            <label for="ubicacion">Ubicación (Aula/Piso):</label>
            <select id="ubicacion" name="Ubicacion" required>
                <option value="">-- Seleccione un Aula --</option>
                <?php foreach ($aulas_ubicacion as $aula): ?>
                    <option value="<?php echo htmlspecialchars($aula['NombreAula']); ?>">
                        <?php echo htmlspecialchars($aula['NombreAula']) . " (" . htmlspecialchars($aula['Nombre_Piso']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="mantenimiento">Último Mantenimiento:</label>
            <input type="date" id="mantenimiento" name="Ultimo_Mantenimiento" value="<?php echo date('Y-m-d'); ?>" required>
            
            <label for="id_tipo_recurso">Tipo de Recurso:</label>
            <select id="id_tipo_recurso" name="ID_Tipo_Recurso" required>
                <option value="">-- Seleccione un Tipo --</option>
                <?php foreach ($tipos_recurso as $tipo): ?>
                    <option value="<?php echo htmlspecialchars($tipo['ID_Tipo_Recurso']); ?>">
                        <?php echo htmlspecialchars($tipo['Nombre_Tipo_Recurso']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Guardar Recurso</button>
        </form>
    </div>
</body>
</html>