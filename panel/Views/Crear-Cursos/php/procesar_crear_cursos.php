<?php
require __DIR__ . "/../../../../conexion.php";
require __DIR__ . "/msj.php";
require __DIR__ . "/fun_cursos.php";

$message_text = '';
$message_type = '';

// Cargar asignaturas para el formulario
$asignaturas = [];
$result = $conn->query("SELECT ID_Asignatura, Nombre FROM asignatura ORDER BY Nombre ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $asignaturas[] = $row;
    }
}

// ==========================================================
// PROCESAR FORMULARIO
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_curso = trim($_POST['nombre_curso'] ?? '');
    $asignaturas_seleccionadas = $_POST['asignaturas'] ?? [];

    if (empty($nombre_curso)) {
        $message_text = "❌ El nombre del curso no puede estar vacío.";
        $message_type = "error";
    } elseif (empty($asignaturas_seleccionadas)) {
        $message_text = "❌ Debe seleccionar al menos una asignatura.";
        $message_type = "error";
    } else {
        // Intentar crear el curso usando tu función
        if (crear_curso($conn, $nombre_curso, $asignaturas_seleccionadas)) {
            $_SESSION['message'] = [
                'text' => "✅ Curso '$nombre_curso' creado con éxito.",
                'type' => 'success',
                'show_gif' => true // bandera para mostrar GIF
            ];
            header("Location: ../Crear-Cursos/crear-Cursos.php");
            exit();
        } else {
            $message_text = "❌ Error al crear el curso.";
            $message_type = "error";
        }
    }
}
