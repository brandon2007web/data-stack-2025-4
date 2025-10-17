<?php
session_start();
// NOTA IMPORTANTE: Asegúrate que esta ruta a "conexion.php" sea correcta
// La ruta que tienes es: __DIR__ . "../../../../conexion.php"
include __DIR__ . "../../../../conexion.php";

header('Content-Type: application/json');

$usuario_rol = intval($_SESSION['rol'] ?? 0);
$usuario_id = intval($_SESSION['usuario_id'] ?? 0);

// Función de respuesta centralizada para manejar errores
function sendResponse(bool $ok, string $error = null, array $debug = []) {
    // Si la conexión sigue abierta y es válida, la cerramos antes de salir.
    global $conn;
    if ($conn && $conn->ping()) {
        $conn->close();
    }
    
    http_response_code($ok ? 200 : 500);
    echo json_encode(['ok' => $ok, 'error' => $error, 'debug_info' => $debug]);
    exit;
}

// --------------------------------------------------------------------------------
// VALIDACIÓN DE ACCESO Y DATOS
// --------------------------------------------------------------------------------

// Solo Admin (1) y Profesor (2) pueden guardar asistencia
if(!in_array($usuario_rol,[1,2])){
    sendResponse(false, 'No tiene permisos para registrar asistencias.', ['rol_actual' => $usuario_rol]);
}

// Asegúrate que la conexión a la DB existe y es válida
if (!isset($conn) || !$conn || $conn->connect_error) {
    sendResponse(false, 'Error de conexión a la base de datos.', ['conn_error' => $conn->connect_error ?? 'Conexión nula']);
}

// ---------------------------
// DATOS ADICIONALES REQUERIDOS POR LA NUEVA TABLA
// ---------------------------
$docenteID = $usuario_id; // Campo ID_Docente (ID del usuario logueado)
$rolMarcado = $usuario_rol; // Campo MarcadoPorRol (Rol del usuario logueado)

// Validar y sanear datos POST
$grupo = filter_input(INPUT_POST, 'grupo', FILTER_VALIDATE_INT);
$hora = filter_input(INPUT_POST, 'hora', FILTER_VALIDATE_INT);
$dia = filter_input(INPUT_POST, 'dia', FILTER_VALIDATE_INT);
$justificacion = trim($_POST['justificacion'] ?? '');

$datos_recibidos = ['grupo'=>$grupo, 'hora'=>$hora, 'dia'=>$dia, 'justificacion_len'=>strlen($justificacion), 'docente_id'=>$docenteID, 'rol'=>$rolMarcado];

if(!$grupo || !$hora || !$dia || empty($justificacion)){
    sendResponse(false, 'Datos POST incompletos o inválidos.', $datos_recibidos);
}

// --------------------------------------------------------------------------------
// OPERACIÓN DE BASE DE DATOS (UPSERT - Insertar o Actualizar)
// --------------------------------------------------------------------------------

try {
    // IMPORTANTE: El funcionamiento del ON DUPLICATE KEY UPDATE depende de que 
    // la tabla 'asistencia' tenga una CLAVE ÚNICA que incluya los campos:
    // (ID_Grupo, ID_Hora, ID_Dia, Fecha)
    $sql = "INSERT INTO asistencia (ID_Grupo, ID_Hora, ID_Dia, ID_Docente, Fecha, Justificacion, MarcadoPorRol) 
             VALUES (?, ?, ?, ?, CURDATE(), ?, ?)
             ON DUPLICATE KEY UPDATE 
             Justificacion = VALUES(Justificacion),
             ID_Docente = VALUES(ID_Docente),
             MarcadoPorRol = VALUES(MarcadoPorRol)";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Tipos de vinculación: i, i, i, i, s, i
    // Grupo (i), Hora (i), Día (i), DocenteID (i), Justificacion (s), RolMarcado (i)
    $stmt->bind_param("iiiisi", $grupo, $hora, $dia, $docenteID, $justificacion, $rolMarcado);
    
    if($stmt->execute()){
        $rows_affected = $stmt->affected_rows;
        
        // Verifica si la fila fue INSERTADA (1) o ACTUALIZADA (2)
        if ($rows_affected > 0) {
            sendResponse(true, null, ['rows_affected' => $rows_affected, 'message' => ($rows_affected == 1 ? 'Insertado' : 'Actualizado')]);
        } else {
            // Este caso puede ocurrir si el registro ya existe y los datos de justificación NO CAMBIARON.
            // Lo consideramos éxito, pero enviamos una advertencia.
            sendResponse(true, null, ['rows_affected' => $rows_affected, 'message' => 'Ejecutado, pero no se afectaron filas (datos idénticos).']);
        }
        
    } else {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    // Si hay un error de MySQL (p. ej., error 1062 de clave duplicada), lo reportamos.
    sendResponse(false, 'Error de servidor al guardar: ' . $e->getMessage(), ['sql_error' => $e->getMessage(), 'sql_state' => $conn->sqlstate ?? 'N/A']);
}
?>
