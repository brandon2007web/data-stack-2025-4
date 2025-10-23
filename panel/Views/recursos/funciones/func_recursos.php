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