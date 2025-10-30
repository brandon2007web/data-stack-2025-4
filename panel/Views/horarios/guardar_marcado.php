<?php
include __DIR__ . "../../../../conexion.php";

// Leer datos JSON
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) { 
    echo "No hay datos"; 
    exit; 
}

// Preparar statement solo una vez
$stmt = $conn->prepare("
    INSERT INTO horario_marcado (id_grupo, id_dia, id_hora, estado)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE estado = VALUES(estado)
");

if (!$stmt) {
    echo "Error en la preparación del statement: " . $conn->error;
    exit;
}

foreach($input as $id_grupo => $dias) {
    foreach($dias as $id_dia => $horas) {
        foreach($horas as $id_hora => $estado) {
            $stmt->bind_param("iiii", $id_grupo, $id_dia, $id_hora, $estado);
            if (!$stmt->execute()) {
                echo "Error al guardar horario: " . $stmt->error;
                exit;
            }
        }
    }
}

$stmt->close();
$conn->close();
echo "ok";
?>
