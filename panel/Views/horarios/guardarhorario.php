<?php
session_start();
include __DIR__ . "../../../../conexion.php";
header("Content-Type: application/json; charset=UTF-8");

// leer JSON
$input = json_decode(file_get_contents("php://input"), true);

$nombreHorario = trim($input['nombre'] ?? '');
$grupoID = intval($input['grupoID'] ?? 0);
$datos = $input['datos'] ?? [];

// fallback a session si no viene por JSON
if (!$grupoID && isset($_SESSION['grupo_id'])) {
    $grupoID = intval($_SESSION['grupo_id']);
}

if (!$nombreHorario || !$grupoID || empty($datos)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan datos para guardar el horario',
        'debug' => ['nombreHorario' => $nombreHorario, 'grupoID' => $grupoID, 'items' => count($datos)]
    ]);
    exit;
}

/* ---------- detectar columnas reales de la tabla "horarios" ---------- */
$columns = [];
$resCols = $conn->query("SHOW COLUMNS FROM horarios");
if ($resCols) {
    while ($c = $resCols->fetch_assoc()) {
        $columns[] = $c['Field'];
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo leer la estructura de la tabla horarios: '.$conn->error]);
    exit;
}

/* ---------- construir INSERT para la tabla "horarios" dinámicamente ---------- */
$insertCols = [];
$insertValues = []; // usar placeholders ? o expresiones como NOW()
$bindTypes = '';
$bindParams = [];

$insertCols[] = 'Nombre';
$insertValues[] = '?';
$bindTypes .= 's';
$bindParams[] = $nombreHorario;

// Si la tabla tiene ID_Grupo, lo agregamos
if (in_array('ID_Grupo', $columns)) {
    $insertCols[] = 'ID_Grupo';
    $insertValues[] = '?';
    $bindTypes .= 'i';
    $bindParams[] = $grupoID;
}

// Si la tabla tiene Fecha_Creacion, insertamos NOW() (no placeholder)
if (in_array('Fecha_Creacion', $columns)) {
    $insertCols[] = 'Fecha_Creacion';
    $insertValues[] = 'NOW()'; // expresión SQL, no bind
}

// armar SQL final
$sqlInsert = "INSERT INTO horarios (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $insertValues) . ")";

mysqli_begin_transaction($conn);
try {
    $stmt = $conn->prepare($sqlInsert);
    if (!$stmt) {
        throw new Exception("Error en prepare(): " . $conn->error . " -- SQL: " . $sqlInsert);
    }

    // bind dinámico si hay parámetros
    if (!empty($bindParams)) {
        // bind_param requiere referencias en un array
        $refs = [];
        foreach ($bindParams as $k => $v) $refs[$k] = &$bindParams[$k];
        array_unshift($refs, $bindTypes);
        // call_user_func_array
        if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
            throw new Exception("Error en bind_param: " . $stmt->error);
        }
    }

    if (!$stmt->execute()) {
        throw new Exception("Error en execute(): " . $stmt->error);
    }
    $idHorario = $stmt->insert_id;
    $stmt->close();

    // preparar detalle
    $sqlDetalle = "
        INSERT INTO horario_detalle (ID_Horario, ID_Dia, ID_Hora, ID_Asignatura, ID_Grupo)
        VALUES (?, ?, ?, ?, ?)
    ";
    $stmtDet = $conn->prepare($sqlDetalle);
    if (!$stmtDet) throw new Exception("Error en prepare detalle: " . $conn->error);

    // bind fijo: ii iii -> 5 enteros
    foreach ($datos as $item) {
        $idDia = intval($item['dia']);
        $idHora = intval($item['hora']);
        $idAsignatura = intval($item['materia']);
        // cada bloque puede traer su propio grupo, si no usamos el global
        $idGrupo = intval($item['grupo'] ?? $grupoID);

        $stmtDet->bind_param("iiiii", $idHorario, $idDia, $idHora, $idAsignatura, $idGrupo);
        if (!$stmtDet->execute()) {
            throw new Exception("Error en execute detalle: " . $stmtDet->error);
        }
    }

    $stmtDet->close();
    mysqli_commit($conn);

    echo json_encode(['status' => 'success', 'message' => 'Horario guardado correctamente', 'idHorario' => $idHorario]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar: ' . $e->getMessage()]);
}

$conn->close();
