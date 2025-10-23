<?php
session_start();
include("../../../conexion.php");

include(__DIR__ . '/mensaje.php');
include(__DIR__ . '/funciones_asignaturas.php');
include(__DIR__ . '/funciones_curso.php');


$id_curso = $_GET['id'] ?? null;
if (!$id_curso || !is_numeric($id_curso)) {
    die("❌ ID de curso inválido.");
}
$id_curso = intval($id_curso);

$mensaje = obtener_mensaje();
$curso = obtener_curso($conn, $id_curso);
if (!$curso) die("❌ Curso no encontrado.");

$curso_nombre = htmlspecialchars($curso['Nombre']);
$asociadas = obtener_asignaturas_asociadas($conn, $id_curso);
$asignaturas = obtener_todas_asignaturas($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevas = $_POST['asignaturas'] ?? [];

    $conn->begin_transaction();
    if (eliminar_relaciones_curso($conn, $id_curso) && insertar_relaciones_curso($conn, $id_curso, $nuevas)) {
        $conn->commit();
        set_mensaje("✅ Asignaturas de $curso_nombre actualizadas correctamente.", "success");
        header("Location: editar-curso-asignaturas.php?id=$id_curso");
        exit;
    } else {
        $conn->rollback();
        $mensaje = ['text' => '❌ Error al actualizar las asignaturas.', 'type' => 'error'];
    }
}
?>