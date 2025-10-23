<?php
session_start();
include("../../../conexion.php");
 // Asegúrate de que la ruta sea correcta

$message_text = '';
$message_type = '';

if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    unset($_SESSION['message']);
}

// Obtener todos los cursos
$cursos = [];
$sql_cursos = "SELECT ID_Curso, Nombre FROM curso ORDER BY Nombre ASC";
$result_cursos = $conn->query($sql_cursos);

if ($result_cursos) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
} else {
    $message_text = "❌ Error al cargar los cursos: " . $conn->error;
    $message_type = 'error';
}
?>