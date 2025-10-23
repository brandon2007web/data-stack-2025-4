<?php
session_start();
include("../../../conexion.php");

// Evitar acceso directo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../index.php");
    exit;
}

// === 1. Recolección y saneamiento de datos ===
$id_recurso = $_POST['ID_Recurso'] ?? 0;
$id_usuario = $_POST['ID_Usuario'] ?? 0;
$proposito  = trim($_POST['Descripcion_Motivo'] ?? '');
$estado     = 'Pendiente';
$id_aulas   = $_POST['ID_Aulas'] ?? null;

// Fechas y horas (desde datetime-local)
$inicio_reserva = $_POST['Inicio_Reserva'] ?? ''; // YYYY-MM-DDTHH:MM
$fin_reserva    = $_POST['Fin_Reserva'] ?? '';   // YYYY-MM-DDTHH:MM

// Separar fecha y hora de inicio
$partes_inicio = explode('T', $inicio_reserva);
$fecha_inicio  = $partes_inicio[0] ?? '';
$hora_inicio   = $partes_inicio[1] ?? '';

// Separar fecha y hora de fin
$partes_fin = explode('T', $fin_reserva);
$fecha_fin  = $partes_fin[0] ?? '';
$hora_fin   = $partes_fin[1] ?? '';

// DATETIME completos
$dt_inicio = "$fecha_inicio $hora_inicio";
$dt_fin    = "$fecha_fin $hora_fin";

// === 2. Validaciones básicas ===
if ($id_usuario <= 0) {
    $_SESSION['message'] = ['type' => 'error', 'text' => '❌ Error: usuario no válido. Inicie sesión nuevamente.'];
    header("Location: reservas.php");
    exit;
}

if (empty($id_recurso) || empty($id_aulas) || empty($fecha_inicio) || empty($fecha_fin) || empty($hora_inicio) || empty($hora_fin)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => '❌ Faltan datos esenciales (Recurso, Aula, Fecha u Hora).'];
    header("Location: reservas.php");
    exit;
}

if (strtotime($dt_inicio) >= strtotime($dt_fin)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => '❌ La hora de inicio debe ser anterior a la hora de fin.'];
    header("Location: reservas.php");
    exit;
}

// === 3. Verificar solapamiento de RECURSO ===
$sql_check_recurso = "
    SELECT ID_Reserva FROM reserva
    WHERE ID_Recurso = ?
    AND (Estado = 'Pendiente' OR Estado = 'Confirmada')
    AND (
        (CONCAT(Fecha_Inicio, ' ', Hora_Inicio) < ?)
        AND (CONCAT(Fecha_Fin, ' ', Hora_Inicio) > ?)
    )
";

$stmt_check_r = $conn->prepare($sql_check_recurso);
$stmt_check_r->bind_param("sss", $id_recurso, $dt_fin, $dt_inicio);
$stmt_check_r->execute();
$stmt_check_r->store_result();

if ($stmt_check_r->num_rows > 0) {
    $stmt_check_r->close();
    $_SESSION['message'] = ['type' => 'error', 'text' => '❌ El recurso seleccionado ya está reservado en ese horario.'];
    header("Location: reservas.php");
    exit;
}
$stmt_check_r->close();

// === 4. Verificar solapamiento de AULA ===
if ($id_aulas !== null && $id_aulas > 0) {
    $sql_check_aula = "
        SELECT ID_Reserva FROM reserva
        WHERE ID_Aulas = ?
        AND (Estado = 'Pendiente' OR Estado = 'Confirmada')
        AND (
            (CONCAT(Fecha_Inicio, ' ', Hora_Inicio) < ?)
            AND (CONCAT(Fecha_Fin, ' ', Hora_Inicio) > ?)
        )
    ";

    $stmt_check_a = $conn->prepare($sql_check_aula);
    $stmt_check_a->bind_param("sss", $id_aulas, $dt_fin, $dt_inicio);
    $stmt_check_a->execute();
    $stmt_check_a->store_result();

    if ($stmt_check_a->num_rows > 0) {
        $stmt_check_a->close();
        $_SESSION['message'] = ['type' => 'error', 'text' => '❌ El aula seleccionada ya está reservada en ese horario.'];
        header("Location: reservas.php");
        exit;
    }
    $stmt_check_a->close();
}

// === 5. Insertar la reserva ===
$sql_insert = "
    INSERT INTO reserva 
    (Fecha_Creada, Hora_Inicio, Estado, Descripcion_Motivo, ID_Usuario, ID_Aulas, ID_Recurso, Fecha_Inicio, Fecha_Fin) 
    VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt_insert = $conn->prepare($sql_insert);
if (!$stmt_insert) {
    $_SESSION['message'] = ['type' => 'error', 'text' => '❌ Error al preparar la inserción: ' . htmlspecialchars($conn->error)];
    header("Location: reservas.php");
    exit;
}

// Tipos: sssiiiss → (Hora, Estado, Motivo, Usuario, Aula, Recurso, FechaI, FechaF)
$stmt_insert->bind_param(
    "sssiiiss",
    $hora_inicio,
    $estado,
    $proposito,
    $id_usuario,
    $id_aulas,
    $id_recurso,
    $fecha_inicio,
    $fecha_fin
);

if ($stmt_insert->execute()) {
    $nuevo_id = $conn->insert_id;
    $stmt_insert->close();

    $_SESSION['message'] = ['type' => 'success', 'text' => "✅ Reserva registrada exitosamente con ID: $nuevo_id."];
    header("Location: reservas.php");
    exit;
} else {
    $error_detail = htmlspecialchars($conn->error);
    $_SESSION['message'] = ['type' => 'error', 'text' => "❌ Error al registrar la reserva: $error_detail"];
    $stmt_insert->close();
    header("Location: crear_reserva.php");
    exit;
}
?>
