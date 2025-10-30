<?php
include(__DIR__."/../../../../conexion.php"); // ← faltaba una barra /

// Incluye la función si depende de otro archivo

// Verificar si se recibió grupo_id
if (isset($_GET['grupo_id']) && is_numeric($_GET['grupo_id'])) {
    $grupo_id = (int)$_GET['grupo_id'];
}

// Llamada a la función para generar la tabla
generarTablaHorario($conn, $grupo_id);



function generarTablaHorario(mysqli $conn, int $grupo_id) {

    // ================================================
    // 1. OBTENER EL ID_TURNO DEL GRUPO
    // ================================================
    $stmt_turno = $conn->prepare("SELECT ID_Turno FROM grupo WHERE ID_Grupo = ?");
    if (!$stmt_turno) {
        echo "<tr><td colspan='7'>Error al preparar la consulta de grupo: " . $conn->error . "</td></tr>";
        return;
    }

    $stmt_turno->bind_param("i", $grupo_id);
    $stmt_turno->execute();
    $result_turno = $stmt_turno->get_result();
    $turno = null;

    if ($result_turno && $result_turno->num_rows > 0) {
        $row_turno = $result_turno->fetch_assoc();
        $turno = (int)$row_turno['ID_Turno'];
    }
    $stmt_turno->close();

    // ================================================
    // 2. DEFINIR HORA SEGÚN TURNO
    // ================================================
    $inicio_hora_str = "07:00"; // Valor por defecto

    if ($turno === 1) {
        $inicio_hora_str = "07:00"; // Turno Mañana
    } elseif ($turno === 2) {
        $inicio_hora_str = "13:35"; // Turno Tarde
    } elseif ($turno === 3) {
        $inicio_hora_str = "18:00"; // Turno Noche
    }

    // ================================================
    // 3. OBTENER BLOQUES DE HORAS
    // ================================================
    $sql_horas = "SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora ASC";
    $result_horas = $conn->query($sql_horas);

    // ================================================
    // 4. GENERAR LA TABLA
    // ================================================
    if ($result_horas && $result_horas->num_rows > 0) {
        $inicio = strtotime($inicio_hora_str);
        $contador_fila = 0;

        while ($row = $result_horas->fetch_assoc()) {
            $idHora     = (int)$row['ID_Hora'];
            $nombre     = htmlspecialchars($row['Nombre']);
            $duracion   = (int)$row['Duracion'];

            $nombre_lower = strtolower($nombre);
            $es_bloque_clase = $nombre_lower !== 'recreo' && $nombre_lower !== 'pausa';

            $hora_inicio = date('H:i', $inicio);
            $hora_fin    = date('H:i', strtotime("+$duracion minutes", $inicio));

            if (!$es_bloque_clase) {
                // Fila para recreo o pausa
                echo "<tr>
                        <td colspan='2' class='num'>{$nombre}</td>
                        <td colspan='5' class='hora bg-gray-200 font-bold'>{$hora_inicio} - {$hora_fin}</td>
                      </tr>";
            } else {
                // Fila de clase
                $contador_fila++;
                echo "<tr>
                        <td class='font-semibold'>{$nombre}</td>
                        <td>{$hora_inicio} - {$hora_fin}</td>
                        <td data-dia='1' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                        <td data-dia='2' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                        <td data-dia='3' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                        <td data-dia='4' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                        <td data-dia='5' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                      </tr>";
            }

            // Avanzar al siguiente bloque
            $inicio = strtotime("+$duracion minutes", $inicio);
        }
    } else {
        echo "<tr><td colspan='7' class='text-center text-red-500'>No hay bloques de horas cargadas en la BD.</td></tr>";
    }
}
?>
