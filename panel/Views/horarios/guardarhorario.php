<?php
include __DIR__ . "../../../../conexion.php";
header("Content-Type: application/json; charset=UTF-8");

// Leer JSON
$input = json_decode(file_get_contents("php://input"), true);

$nombreHorario = trim($input['nombre'] ?? '');
$grupoID = intval($input['grupoID'] ?? 0);
$datos = $input['datos'] ?? [];

if (!$nombreHorario || !$grupoID || empty($datos)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan datos para guardar el horario',
        'nombreHorario' => $nombreHorario,
        'grupoID' => $grupoID,
        'datosHorario' => $datos
    ]);
    exit;
}

// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Insertar el horario
    $stmt = $conn->prepare("INSERT INTO horarios (Nombre, Fecha_Creacion) VALUES (?, NOW())");
    $stmt->bind_param("s", $nombreHorario);
    $stmt->execute();
    $idHorario = $stmt->insert_id;
    $stmt->close();

    // Insertar cada bloque del horario
    $stmtDetalle = $conn->prepare("
        INSERT INTO horario_detalle (ID_Horario, ID_Dia, ID_Hora, ID_Asignatura, ID_Grupo)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($datos as $item) {
        $idDia = intval($item['dia']);
        $idHora = intval($item['hora']);
        $idAsignatura = intval($item['materia']);
        $idGrupo = intval($item['grupo']); // ⚡ Tomamos el ID del grupo de cada bloque

        $stmtDetalle->bind_param("iiiii", $idHorario, $idDia, $idHora, $idAsignatura, $idGrupo);
        $stmtDetalle->execute();
    }

    $stmtDetalle->close();
    mysqli_commit($conn);

    echo json_encode(['status' => 'success', 'message' => 'Horario guardado correctamente']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar: '.$e->getMessage()]);
}

$conn->close();
?>
