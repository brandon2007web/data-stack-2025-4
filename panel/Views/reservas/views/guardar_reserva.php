<?php
session_start();
include(__DIR__ . "/../../../../conexion.php");

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'error' => '', 'debug' => []];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Usa POST.');
    }

    // === Obtener datos del formulario ===
    $sala        = trim($_POST['sala'] ?? '');
    $nombre      = trim($_POST['nombre'] ?? '');
    $fecha       = trim($_POST['fecha'] ?? '');
    $hora_inicio = trim($_POST['hora_inicio'] ?? '');
    $hora_fin    = trim($_POST['hora_fin'] ?? '');
    $motivo      = trim($_POST['motivo'] ?? '');
    $id_usuario  = $_SESSION['usuario_id'] ?? 1;

    if (!$sala || !$nombre || !$fecha || !$hora_inicio || !$hora_fin) {
        throw new Exception('Faltan campos obligatorios.');
    }

    // === Normalizar formatos ===
    $hora_inicio_full = preg_match('/^\d{2}:\d{2}$/', $hora_inicio) ? "$hora_inicio:00" : $hora_inicio;
    $hora_fin_full    = preg_match('/^\d{2}:\d{2}$/', $hora_fin) ? "$hora_fin:00" : $hora_fin;

    $fecha_inicio = "$fecha $hora_inicio_full";
    $fecha_fin    = "$fecha $hora_fin_full";

    // Si la hora fin es anterior o igual a la de inicio, pasa al día siguiente
    if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
        $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_fin . ' +1 day'));
    }

    $response['debug']['valores'] = compact('sala','nombre','fecha_inicio','fecha_fin','hora_inicio_full','hora_fin_full');

    // === VALIDAR SOLAPAMIENTO SOLO EN LA MISMA SALA ===
    $sql_conflicto = "
        SELECT ID_Reserva 
        FROM reserva 
        WHERE ID_Aulas = ? 
          AND Estado = 'Activa'
          AND (
                (Fecha_Inicio <= ? AND Fecha_Fin > ?) OR
                (Fecha_Inicio < ? AND Fecha_Fin >= ?) OR
                (? <= Fecha_Inicio AND ? > Fecha_Inicio)
              )
        LIMIT 1
    ";
    $check = $conn->prepare($sql_conflicto);
    $check->bind_param("issssss", $sala, $fecha_inicio, $fecha_inicio, $fecha_fin, $fecha_fin, $fecha_inicio, $fecha_fin);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        throw new Exception("⚠️ Ya existe una reserva activa en esta sala y horario.");
    }
    $check->close();

    // === INSERTAR NUEVA RESERVA ===
    $stmt = $conn->prepare("
        INSERT INTO reserva 
        (Fecha_Creada, Hora_Inicio, Estado, Descripcion_Motivo, ID_Usuario, ID_Aulas, Fecha_Inicio, Fecha_Fin)
        VALUES (NOW(), ?, 'Activa', ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Error en prepare(): " . $conn->error);
    }

    $stmt->bind_param("ssiiss", $hora_inicio_full, $motivo, $id_usuario, $sala, $fecha_inicio, $fecha_fin);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar: " . $stmt->error);
    }

    $response['success'] = true;
    $response['debug']['insert_id'] = $stmt->insert_id;

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
