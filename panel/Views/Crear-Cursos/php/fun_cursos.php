<?php
function crear_curso($conn, $nombre_curso, $asignaturas) {
    $conn->begin_transaction();
    $success = true;

    $sql_curso = "INSERT INTO curso (Nombre) VALUES (?)";
    $stmt_curso = $conn->prepare($sql_curso);
    $stmt_curso->bind_param("s", $nombre_curso);

    if ($stmt_curso->execute()) {
        $id_curso_nuevo = $conn->insert_id;
        $stmt_curso->close();

        $sql_asig = "INSERT INTO curso_tiene_asignaturas (ID_Asignatura, ID_Curso) VALUES (?, ?)";
        $stmt_rel = $conn->prepare($sql_asig);
        foreach ($asignaturas as $id_asig) {
            $id_asig = intval($id_asig);
            $stmt_rel->bind_param("ii", $id_asig, $id_curso_nuevo);
            if (!$stmt_rel->execute()) {
                $success = false;
                break;
            }
        }
        $stmt_rel->close();
    } else {
        $success = false;
    }

    if ($success) {
        $conn->commit();
        return true;
    } else {
        $conn->rollback();
        return false;
    }
}
?>
