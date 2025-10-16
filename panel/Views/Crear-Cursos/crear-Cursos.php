<?php
session_start();
require "../../../conexion.php"; // Asegúrate de que este archivo establece la conexión $conn (mysqli)

// =========================================================
// 0. MANEJO DE MENSAJES Y OBTENCIÓN DE DATOS NECESARIOS
// =========================================================
$message_text = '';
$message_type = '';

if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    unset($_SESSION['message']);
}

// Obtener todas las asignaturas existentes para el formulario
$asignaturas = [];
$sql_asignaturas = "SELECT ID_Asignatura, Nombre FROM asignatura ORDER BY Nombre ASC";
$result_asignaturas = $conn->query($sql_asignaturas);
if ($result_asignaturas) {
    while ($row = $result_asignaturas->fetch_assoc()) {
        $asignaturas[] = $row;
    }
} else {
    // Manejo de error si no se pueden cargar las asignaturas
    $message_text = "❌ Error al cargar las asignaturas: " . $conn->error;
    $message_type = 'error';
}

// =========================================================
// 1. PROCESAMIENTO: CREAR CURSO Y ASIGNAR ASIGNATURAS (POST SEGURO)
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nombre_curso = trim($_POST['nombre_curso'] ?? '');
    // El array de IDs de asignaturas seleccionadas
    $asignaturas_seleccionadas = $_POST['asignaturas'] ?? []; 

    // 1.1. Validación básica
    if (empty($nombre_curso)) {
        $message_text = "❌ Error: El nombre del curso no puede estar vacío.";
        $message_type = 'error';
    } elseif (empty($asignaturas_seleccionadas)) {
        $message_text = "❌ Error: Debe seleccionar al menos una asignatura para el curso.";
        $message_type = 'error';
    } else {
        // Iniciar transacción para asegurar que ambos inserts se ejecuten o ninguno
        $conn->begin_transaction();
        $success = true;

        // --- 1.2. PASO 1: INSERTAR EL NUEVO CURSO ---
        $sql_curso = "INSERT INTO curso (Nombre) VALUES (?)";
        if ($stmt_curso = $conn->prepare($sql_curso)) {
            $stmt_curso->bind_param("s", $nombre_curso);

            if ($stmt_curso->execute()) {
                $id_curso_nuevo = $conn->insert_id; // Obtener el ID del curso recién creado
                $stmt_curso->close();

                // --- 1.3. PASO 2: INSERTAR LAS ASIGNATURAS ASOCIADAS ---
                $sql_asignaturas_curso = "INSERT INTO curso_tiene_asignaturas (ID_Asignatura, ID_Curso) VALUES (?, ?)";
                if ($stmt_relacion = $conn->prepare($sql_asignaturas_curso)) {
                    
                    foreach ($asignaturas_seleccionadas as $id_asignatura) {
                        // Aseguramos que el ID de la asignatura sea un entero
                        $id_asignatura = intval($id_asignatura); 
                        
                        // Vincular y ejecutar (ii = dos enteros)
                        $stmt_relacion->bind_param("ii", $id_asignatura, $id_curso_nuevo);
                        
                        if (!$stmt_relacion->execute()) {
                            $success = false;
                            break; // Detener si hay un fallo en la inserción de relación
                        }
                    }
                    $stmt_relacion->close();
                } else {
                    $success = false;
                }
            } else {
                $message_text = "❌ Error al crear el curso: " . $stmt_curso->error;
                $success = false;
            }
        } else {
            $message_text = "❌ Error al preparar la consulta del curso: " . $conn->error;
            $success = false;
        }

        // --- 1.4. FINALIZAR TRANSACCIÓN ---
        if ($success) {
            $conn->commit();
            // Patrón PRG: Redirección con mensaje de éxito
            $_SESSION['message'] = [
                'text' => "✅ Curso **" . htmlspecialchars($nombre_curso) . "** y sus asignaturas fueron creados con éxito.",
                'type' => 'success'
            ];
            header("Location: crear-cursos.php"); 
            exit();
        } else {
            $conn->rollback();
            // Configurar mensaje de error si no se hizo commit
            if (empty($message_text)) {
                $message_text = "❌ Error desconocido al procesar el curso y sus asignaturas.";
            }
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Curso</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f9; color: #333; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 30px; text-align: center; }
        
        /* Formulario y Controles */
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"] {
            width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; 
            border-radius: 5px; box-sizing: border-box;
        }
        button[type="submit"] {
            padding: 12px 25px; background: #28a745; color: white; border: none; 
            border-radius: 5px; cursor: pointer; font-size: 1em; transition: background 0.3s;
        }
        button[type="submit"]:hover { background: #1e7e34; }

        /* Asignaturas Checkbox */
        .asignaturas-list {
            border: 1px solid #ddd; padding: 15px; border-radius: 5px; max-height: 200px; 
            overflow-y: auto; margin-bottom: 20px; background: #f9f9f9;
        }
        .asignaturas-list label {
            display: flex; align-items: center; margin-bottom: 5px; font-weight: normal;
        }
        .asignaturas-list input[type="checkbox"] {
            margin-right: 10px;
        }

        /* Mensajes de Estado */
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .back-link { display: block; margin-top: 20px; color: #007bff; text-decoration: none; font-weight: bold; }
        .action-link { display: inline-block; margin-right: 15px; margin-top: 10px; color: #28a745; text-decoration: none; font-weight: bold; }
    </style>
</head>

<body>
    <div class="container">
        <h1>Crear Nuevo Curso y Asignar Asignaturas</h1>

        <?php if ($message_text): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="crear-cursos.php">
            
            <label for="nombre_curso">Nombre del Curso:</label>
            <input type="text" name="nombre_curso" id="nombre_curso" 
                   placeholder="Ej: Bachillerato Informática" required maxlength="255">

            <label>Asignaturas del Curso (Seleccione una o varias):</label>
            <div class="asignaturas-list">
                <?php if (count($asignaturas) > 0): ?>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <label>
                            <input type="checkbox" name="asignaturas[]" 
                                   value="<?php echo htmlspecialchars($asignatura['ID_Asignatura']); ?>">
                            <?php echo htmlspecialchars($asignatura['Nombre']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>⚠️ No hay asignaturas creadas. Por favor, cree asignaturas primero.</p>
                <?php endif; ?>
            </div>

            <button type="submit">Crear Curso </button>
        </form>
        
        <a href="listar_Cursos.php" class="action-link">📋 Ir a la Lista de Cursos para Editar</a>
        <a href="../../index.php" class="back-link">⬅ Volver al Panel Principal</a>
    </div>
</body>
</html>