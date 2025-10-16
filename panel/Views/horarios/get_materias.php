<?php
include __DIR__ . "../../../../conexion.php";
header("Content-Type: application/json");

$id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;

if (!$id_grupo) {
    echo json_encode(["ok" => false, "materias" => []]);
    exit;
}

// Obtener ID_Curso del grupo
$stmt = $conn->prepare("SELECT ID_Curso FROM grupo WHERE ID_Grupo = ?");
$stmt->bind_param("i", $id_grupo);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "materias" => []]);
    exit;
}
$id_curso = $res->fetch_assoc()['ID_Curso'];
$stmt->close();

// Obtener materias del curso
$sql = "SELECT a.ID_Asignatura AS id, a.Nombre AS nombre
        FROM curso_tiene_asignaturas cta
        JOIN asignatura a ON a.ID_Asignatura = cta.ID_Asignatura
        WHERE cta.ID_Curso = ?
        ORDER BY a.Nombre ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];
while ($row = $result->fetch_assoc()) {
    $materias[] = $row;
}

$stmt->close();
echo json_encode(["ok" => true, "materias" => $materias]);
