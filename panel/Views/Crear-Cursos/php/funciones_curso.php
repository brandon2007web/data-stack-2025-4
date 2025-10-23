<?php
function obtener_curso($conn, $id_curso) {
    $stmt = $conn->prepare("SELECT Nombre FROM curso WHERE ID_Curso = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function eliminar_relaciones_curso($conn, $id_curso) {
    $stmt = $conn->prepare("DELETE FROM curso_tiene_asignaturas WHERE ID_Curso = ?");
    $stmt->bind_param("i", $id_curso);
    return $stmt->execute();
}
?>
