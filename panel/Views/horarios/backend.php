<?php
include __DIR__ . "../../../../conexion.php";
header("Content-Type: application/json");

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!isset($data['horario']) || !isset($data['id_grupo'])) {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit;
}

$id_grupo = (int)$data['id_grupo'];

foreach ($data['horario'] as $item) {
    $dia = (int)$item['dia'];
    $bloque = (int)$item['bloque'];
    $materia = $conn->real_escape_string($item['materia']);

    $conn->query("INSERT IGNORE INTO asignatura (Nombre) VALUES ('$materia')");
    $res = $conn->query("SELECT ID_Asignatura FROM asignatura WHERE Nombre='$materia'");
    $id_asignatura = $res->fetch_assoc()['ID_Asignatura'];

    $conn->query("INSERT INTO horario (ID_Grupo, Dia, Bloque, ID_Asignatura) 
                  VALUES ($id_grupo, $dia, $bloque, $id_asignatura)");
}

echo json_encode(["ok" => true, "msg" => "Horario guardado con éxito"]);
