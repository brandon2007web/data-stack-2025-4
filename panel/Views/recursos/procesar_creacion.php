<?php
// ¡IMPORTANTE! Iniciar la sesión al principio
session_start();

// Incluye la conexión a la base de datos.
// ASEGÚRATE DE QUE ESTA RUTA SEA CORRECTA
include("../../../conexion.php"); 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si alguien intenta acceder directamente, lo redirigimos al formulario
    header("Location: crear_recurso.php");
    exit;
}

// 1. Recoger y sanear los datos del formulario
$nombre = $_POST['Nombre'] ?? '';
$ubicacion = $_POST['Ubicacion'] ?? '';
$descripcion = $_POST['Descripcion'] ?? '';
$ultimo_mantenimiento = $_POST['Ultimo_Mantenimiento'] ?? date('Y-m-d');
$id_tipo_recurso = $_POST['ID_Tipo_Recurso'] ?? 0;

// Definición del estado por defecto (asumido ya que no viene del formulario)
$estado = 'Disponible'; 

// Validar que los campos críticos no estén vacíos.
if (empty($nombre) || empty($ubicacion) || empty($id_tipo_recurso)) {
    // Almacenar mensaje de error en la sesión
    $_SESSION['message'] = [
        'type' => 'error',
        'text' => '❌ Error: Faltan campos obligatorios (Nombre, Ubicación o Tipo de Recurso).',
    ];
    header("Location: crear_recurso.php"); 
    exit;
}

// 2. Preparar la consulta SQL para la inserción
$sql = "INSERT INTO recursos (Nombre, Ubicacion, Estado, Descripcion, Ultimo_Mantenimiento, ID_Tipo_Recurso) 
        VALUES (?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // 3. Vincular parámetros (5 strings y 1 integer)
    $stmt->bind_param("sssssi", 
        $nombre, 
        $ubicacion, 
        $estado, 
        $descripcion, 
        $ultimo_mantenimiento, 
        $id_tipo_recurso
    );

    // 4. Ejecutar la consulta
    if ($stmt->execute()) {
        // La inserción fue exitosa
        $nuevo_id = $conn->insert_id; 
        $stmt->close();
        
        // Almacenar mensaje de ÉXITO en la sesión
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => '✅ Recurso creado exitosamente con ID: ' . $nuevo_id . '.',
        ];
        header("Location: crearrecursos.php"); 
        exit;
    } else {
        // Error de ejecución SQL
        $stmt->close();
        $error_msg = $conn->error;

        // Almacenar mensaje de error SQL en la sesión
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => '❌ Error al ejecutar la consulta SQL. Detalle: ' . htmlspecialchars($error_msg),
        ];
        header("Location: crearrecursos.php");
        exit;
    }
} else {
    // Error al preparar la consulta
    $error_msg = $conn->error;

    // Almacenar mensaje de error de preparación en la sesión
    $_SESSION['message'] = [
        'type' => 'error',
        'text' => '❌ Error al preparar la consulta. Detalle: ' . htmlspecialchars($error_msg),
    ];
    header("Location: crearrecursos.php");
    exit;
}
?>