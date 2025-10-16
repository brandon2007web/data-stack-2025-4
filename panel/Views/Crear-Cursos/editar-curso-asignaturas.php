<?php
session_start();
require "../conexion.php"; // Asegúrate de que la ruta sea correcta

// =========================================================
// 0. SEGURIDAD Y PREPARACIÓN
// =========================================================
$id_curso = $_GET['id'] ?? null;
$message_text = '';
$message_type = '';
$curso_nombre = 'Curso Desconocido';

if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    unset($_SESSION['message']);
}

// 0.1. Validación del ID
if (!$id_curso || !is_numeric($id_curso) || $id_curso <= 0) {
    die("❌ Error: ID de curso no válido o no especificado.");
}
$id_curso = intval($id_curso);

// 0.2. Obtener nombre del curso y asignaturas actuales
$asignaturas_asociadas = [];
$asignaturas_disponibles = [];

// Obtener nombre del curso
$stmt_curso = $conn->prepare("SELECT Nombre FROM curso WHERE ID_Curso = ?");
if ($stmt_curso) {
    $stmt_curso->bind_param("i", $id_curso);
    $stmt_curso->execute();
    $result_curso = $stmt_curso->get_result();
    if ($result_curso->num_rows > 0) {
        $curso = $result_curso->fetch_assoc();
        $curso_nombre = htmlspecialchars($curso['Nombre']);
    } else {
        die("❌ Error: Curso no encontrado.");
    }
    $stmt_curso->close();
} else {
    die("❌ Error al preparar la consulta del curso: " . $conn->error);
}

// Obtener ASIGNATURAS ASOCIADAS actualmente
$sql_asoc = "SELECT ID_Asignatura FROM curso_tiene_asignaturas WHERE ID_Curso = ?";
$stmt_asoc = $conn->prepare($sql_asoc);
if ($stmt_asoc) {
    $stmt_asoc->bind_param("i", $id_curso);
    $stmt_asoc->execute();
    $result_asoc = $stmt_asoc->get_result();
    while ($row = $result_asoc->fetch_assoc()) {
        // Usamos un array asociativo para buscar IDs rápidamente
        $asignaturas_asociadas[$row['ID_Asignatura']] = true; 
    }
    $stmt_asoc->close();
}

// Obtener todas las ASIGNATURAS DISPONIBLES
$sql_todas = "SELECT ID_Asignatura, Nombre FROM asignatura ORDER BY Nombre ASC";
$result_todas = $conn->query($sql_todas);
if ($result_todas) {
    while ($row = $result_todas->fetch_assoc()) {
        $asignaturas_disponibles[] = $row;
    }
}

// =========================================================
// 1. PROCESAMIENTO: ACTUALIZAR ASIGNATURAS (POST SEGURO)
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // El array de IDs de asignaturas seleccionadas
    $nuevas_asignaturas = $_POST['asignaturas'] ?? []; 

    // 1.1. Validación (Puede ser vacío, si se quiere quitar todas)
    // if (empty($nuevas_asignaturas)) {
    //     $message_text = "⚠️ Advertencia: No seleccionaste ninguna asignatura. Se desasociarán todas.";
    //     $message_type = 'warning';
    // }

    $conn->begin_transaction();
    $success = true;
    
    // --- PASO 1: ELIMINAR RELACIONES ANTIGUAS SOLO PARA ESTE CURSO ---
    // Esto previene duplicados y limpia las asignaturas que fueron deseleccionadas.
    $sql_delete = "DELETE FROM curso_tiene_asignaturas WHERE ID_Curso = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id_curso);
        if (!$stmt_delete->execute()) {
            $message_text = "❌ Error al limpiar relaciones antiguas: " . $stmt_delete->error;
            $success = false;
        }
        $stmt_delete->close();
    } else {
        $message_text = "❌ Error al preparar la eliminación: " . $conn->error;
        $success = false;
    }

    // --- PASO 2: INSERTAR NUEVAS RELACIONES ---
    if ($success && !empty($nuevas_asignaturas)) {
        $sql_insert = "INSERT INTO curso_tiene_asignaturas (ID_Asignatura, ID_Curso) VALUES (?, ?)";
        if ($stmt_insert = $conn->prepare($sql_insert)) {
            
            foreach ($nuevas_asignaturas as $id_asignatura) {
                $id_asignatura = intval($id_asignatura); 
                
                $stmt_insert->bind_param("ii", $id_asignatura, $id_curso);
                
                if (!$stmt_insert->execute()) {
                    $message_text = "❌ Error al insertar la asignatura ID $id_asignatura: " . $stmt_insert->error;
                    $success = false;
                    break;
                }
            }
            $stmt_insert->close();
        } else {
            $message_text = "❌ Error al preparar la inserción de nuevas relaciones: " . $conn->error;
            $success = false;
        }
    }

    // --- 1.4. FINALIZAR TRANSACCIÓN ---
    if ($success) {
        $conn->commit();
        // PRG: Redirección con mensaje de éxito
        $_SESSION['message'] = [
            'text' => "✅ Asignaturas del curso **" . $curso_nombre . "** actualizadas con éxito.",
            'type' => 'success'
        ];
        // Redirigir a la misma página para ver los cambios actualizados
        header("Location: editar-curso-asignaturas.php?id=$id_curso"); 
        exit();
    } else {
        $conn->rollback();
        if (empty($message_text)) {
            $message_text = "❌ Error desconocido al actualizar las asignaturas.";
        }
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Asignaturas: <?php echo $curso_nombre; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f9; color: #333; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 30px; text-align: center; }
        
        /* Formulario y Controles */
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        button[type="submit"] {
            padding: 12px 25px; background: #007bff; color: white; border: none; 
            border-radius: 5px; cursor: pointer; font-size: 1em; transition: background 0.3s; margin-top: 20px;
        }
        button[type="submit"]:hover { background: #0056b3; }

        /* Asignaturas Checkbox */
        .asignaturas-list {
            border: 1px solid #ddd; padding: 15px; border-radius: 5px; max-height: 300px; 
            overflow-y: auto; margin-bottom: 20px; background: #f9f9f9;
        }
        .asignaturas-list label {
            display: flex; align-items: center; margin-bottom: 8px; font-weight: normal;
        }
        .asignaturas-list input[type="checkbox"] {
            margin-right: 10px;
        }

        /* Mensajes de Estado */
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        
        .back-link { display: block; margin-top: 20px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>

<body>
    <div class="container">
        <h1>Editar Asignaturas del Curso: <?php echo $curso_nombre; ?></h1>

        <?php if ($message_text): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="editar-curso-asignaturas.php?id=<?php echo $id_curso; ?>">
            
            <label>Seleccione las asignaturas que pertenecen a **<?php echo $curso_nombre; ?>**:</label>
            <div class="asignaturas-list">
                <?php if (count($asignaturas_disponibles) > 0): ?>
                    <?php foreach ($asignaturas_disponibles as $asignatura): ?>
                        <?php 
                            // Determinar si la casilla debe estar marcada (checked)
                            $checked = isset($asignaturas_asociadas[$asignatura['ID_Asignatura']]) ? 'checked' : ''; 
                        ?>
                        <label>
                            <input type="checkbox" name="asignaturas[]" 
                                   value="<?php echo htmlspecialchars($asignatura['ID_Asignatura']); ?>"
                                   <?php echo $checked; ?>>
                            <?php echo htmlspecialchars($asignatura['Nombre']); ?> (ID: <?php echo $asignatura['ID_Asignatura']; ?>)
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>⚠️ No hay asignaturas creadas.</p>
                <?php endif; ?>
            </div>

            <button type="submit">Guardar Cambios</button>
        </form>
        
        <a href="listar-cursos.php" class="back-link">⬅ Volver a la Lista de Cursos</a>
    </div>
</body>
</html>