<?php
include("../../../conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    // === AGREGAR MATERIA INDIVIDUAL (opcional) ===
    if ($accion === "agregarMateria") {
        $dia     = (int)$_POST["dia"];
        $bloque  = (int)$_POST["bloque"];
        $materia = (int)$_POST["materia"]; 

        $sql = "INSERT INTO horario (ID_Dia, ID_Hora, ID_Asignatura) VALUES ($dia, $bloque, $materia)";
        if ($conn->query($sql)) {
            echo "Materia agregada con éxito";
        } else {
            echo "Error al agregar materia: " . $conn->error;
        }
    }

 // === AGREGAR CURSO Y GRUPO (opcional) ===
elseif ($accion === "agregarCurso" || $accion === "crearGrupo") {
    $curso = (int)$_POST["curso"];
    $grupo = $conn->real_escape_string($_POST["grupo"]);
    $turno = (int)$_POST["turno"];

    // ✅ Faltaban las comillas alrededor del valor de texto $grupo
    $sql = "INSERT INTO grupo (ID_Curso, Nombre, ID_Turno) 
            VALUES ($curso, '$grupo', $turno)";

    if ($conn->query($sql)) {
        echo "✅ Grupo agregado con éxito.";
    } else {
        echo "❌ Error al crear grupo: " . $conn->error;
    }
}


    // === GUARDAR HORARIO COMPLETO ===
    elseif ($accion === "guardarHorario") {
        $horario = json_decode($_POST["horario"], true);
        $nombreHorario = $_POST["nombreHorario"] ?? "Nuevo Horario";

        if ($horario && is_array($horario) && count($horario) > 0) {

            // 1️⃣ Crear un nuevo registro en la tabla 'horarios'
            $stmtCab = $conn->prepare("INSERT INTO horarios (Nombre) VALUES (?)");
            if (!$stmtCab) {
                die("Error al preparar statement: " . $conn->error);
            }

            $stmtCab->bind_param("s", $nombreHorario);
            $stmtCab->execute();
            $idHorario = $stmtCab->insert_id; // ID generado automáticamente
            $stmtCab->close();

            // 2️⃣ Insertar todas las materias en 'horario' con el mismo ID_Horario
            $stmt = $conn->prepare("INSERT INTO horario (ID_Horario, ID_Dia, ID_Hora, ID_Asignatura) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Error al preparar statement: " . $conn->error);
            }

            foreach ($horario as $entrada) {
                $stmt->bind_param(
                    "iiii",
                    $idHorario,
                    $entrada["dia"],    // ID del día
                    $entrada["bloque"], // ID de la hora
                    $entrada["materia"] // ID de la asignatura
                );
                $stmt->execute();
            }

            $stmt->close();

            echo "Horario '$nombreHorario' guardado correctamente con ID $idHorario ✅";

        } else {
            echo "Horario no válido o vacío.";
        }
    }

    else {
        echo "Acción no reconocida.";
    }
}

$conn->close();
?>
