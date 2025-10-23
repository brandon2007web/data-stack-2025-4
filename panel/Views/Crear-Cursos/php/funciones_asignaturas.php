<?php
function obtener_asignaturas_asociadas($conn, $id_curso) {
    $asociadas = [];
    $stmt = $conn->prepare("SELECT ID_Asignatura FROM curso_tiene_asignaturas WHERE ID_Curso = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $asociadas[$row['ID_Asignatura']] = true;
    }
    return $asociadas;
}

function obtener_todas_asignaturas($conn) {
    $resultado = [];
    $sql = "SELECT ID_Asignatura, Nombre FROM asignatura ORDER BY Nombre ASC";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $resultado[] = $row;
    }
    return $resultado;
}

function insertar_relaciones_curso($conn, $id_curso, $asignaturas) {
    $stmt = $conn->prepare("INSERT INTO curso_tiene_asignaturas (ID_Asignatura, ID_Curso) VALUES (?, ?)");
    foreach ($asignaturas as $id_asignatura) {
        $id_asignatura = intval($id_asignatura);
        $stmt->bind_param("ii", $id_asignatura, $id_curso);
        if (!$stmt->execute()) return false;
    }
    return true;
}
?>
