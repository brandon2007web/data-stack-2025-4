<?php
include(__DIR__ . "/../../../../conexion.php");
header('Content-Type: application/json');
$query = $conn->query("
    SELECT 
        r.ID_Reserva,
        a.Nombre AS Nombre_Sala,
        r.Descripcion_Motivo,
        r.Fecha_Inicio,
        r.Fecha_Fin
    FROM reserva r
    LEFT JOIN aulas a ON a.ID_Aula = r.ID_Aulas
");

if(!$query) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$data = [];

while($row = $query->fetch_assoc()) {
    $start = date('Y-m-d\TH:i:s', strtotime($row['Fecha_Inicio']));
    $end   = date('Y-m-d\TH:i:s', strtotime($row['Fecha_Fin']));

    $data[] = [
        'id' => $row['ID_Reserva'],
        'title' => $row['Nombre_Sala'] . ' - ' . $row['Descripcion_Motivo'],
        'start' => $start,
        'end' => $end
    ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
