<?php
// ¡IMPORTANTE! Iniciar la sesión al principio
session_start();

// 1. Incluir el archivo de conexión a la base de datos
// AJUSTA ESTA RUTA si es necesario
include("../../../conexion.php"); 

// Inicializar variables de mensaje
$message_text = '';
$message_type = '';

// Verificar si existe un mensaje en la sesión (enviado por procesar_creacion.php)
if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    
    // Eliminar el mensaje de la sesión para que no se muestre al recargar
    unset($_SESSION['message']);
}

// Variables para almacenar resultados de consultas
$aulas_ubicacion = [];
$tipos_recurso = [];
$error_carga = null; // Para errores generales de conexión/consulta

// 2. Consulta para obtener la lista combinada de Aulas y Pisos (para Ubicación)
$tabla_aulas = 'aulas'; 
$sql_aulas = "SELECT 
                a.ID_Aula, 
                a.Nombre AS NombreAula, 
                p.Nombre_Piso
              FROM {$tabla_aulas} a
              JOIN pisos p ON a.ID_Piso = p.ID_Piso
              ORDER BY p.ID_Piso, NombreAula ASC";

$result_aulas = $conn->query($sql_aulas);

if ($result_aulas) {
    while ($row = $result_aulas->fetch_assoc()) {
        $aulas_ubicacion[] = $row;
    }
} else {
    $error_carga = "❌ Error al cargar aulas/pisos: " . $conn->error;
}

// 3. Consulta para obtener la lista de Tipos de Recursos
$tabla_tipos = 'tipo_recursos';
$sql_tipos = "SELECT ID_Tipo_Recurso, Nombre_Tipo_Recurso FROM {$tabla_tipos} ORDER BY Nombre_Tipo_Recurso ASC";

$result_tipos = $conn->query($sql_tipos);

if ($result_tipos) {
    while ($row = $result_tipos->fetch_assoc()) {
        $tipos_recurso[] = $row;
    }
} else {
    if (!$error_carga) { // Si no había un error anterior, registra este.
        $error_carga = "❌ Error al cargar tipos de recurso: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Recurso</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #34495e; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], input[type="date"], textarea, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 20px; font-size: 16px; }
        button:hover { background-color: #27ae60; }
        .mensaje { padding: 10px; margin-top: 10px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
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