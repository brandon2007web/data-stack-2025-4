<?php
session_start();
include __DIR__ . "../../../../conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] === 'Invitado') {
    // Redirige a otra página o muestra un mensaje
    header("Location: bienvenido.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario Semanal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>HORARIO SEMANAL</h1>

    <table id="horario" border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Hora</th>
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miércoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora ASC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $inicio = strtotime("07:00");
                // *** 1. Inicialización de la variable clave ***
                $contador_fila = 0; 

                while ($row = $result->fetch_assoc()) {
                    $idHora     = (int)$row['ID_Hora'];
                    $nombre     = htmlspecialchars($row['Nombre']);
                    $duracion = (int)$row['Duracion'];
                    
                    $nombre_lower = strtolower($nombre);
                    // Esto asume que solo 'recreo' o 'pausa' no deben tener celdas de materia
                    $es_bloque_clase = $nombre_lower != 'recreo' && $nombre_lower != 'pausa'; 

                    $hora_inicio = date('H:i', $inicio);
                    $hora_fin    = date('H:i', strtotime("+$duracion minutes", $inicio));

                    if (!$es_bloque_clase) {
                        // Fila para recreo/pausa sin celdas de materia
                        echo "<tr>
                                <td colspan='2' class='num'>{$nombre}</td>
                                <td colspan='5' class='hora'>{$hora_inicio} - {$hora_fin}</td>
                            </tr>";
                    } else {
                        // *** 2. Incremento y uso del contador para data-indice-fila ***
                        $contador_fila++;
                        echo "<tr>
                                <td>{$nombre}</td>
                                <td>{$hora_inicio} - {$hora_fin}</td>
                                <td data-dia='1' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                                <td data-dia='2' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                                <td data-dia='3' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                                <td data-dia='4' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                                <td data-dia='5' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                            </tr>";
                    }

                    $inicio = strtotime("+$duracion minutes", $inicio);
                }
            } else {
                echo "<tr><td colspan='7'>No hay horas cargadas en la BD</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <button id="guardarBtn" style="display:none">Guardar Horario</button>

<div class="formularios">

    <h2>Agregar Materia al Horario</h2>

    <div class="formulario">
        <label for="nombreHorario">Nombre del Horario:</label>
<input type="text" id="nombreHorario" placeholder="Ingrese un nombre">
       
        <label for="grupo">Grupo:</label>
        <select id="grupo">
            <option value="">-- Seleccione --</option>
            <?php
            $resGrupos = $conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre ASC");
            while ($row = $resGrupos->fetch_assoc()) {
                echo "<option value='{$row['ID_Grupo']}'>" . htmlspecialchars($row['Nombre']) . "</option>";
            }
            ?>
        </select>

        <label for="dia">Día:</label>
        <select id="dia">
            <option value="1">Lunes</option>
            <option value="2">Martes</option>
            <option value="3">Miércoles</option>
            <option value="4">Jueves</option>
            <option value="5">Viernes</option>
        </select>

        <label for="bloque">Hora:</label>
        <select id="bloque">
            <?php
            // Se filtra para que solo salgan horas de clase en el selector
            $resHoras = $conn->query("SELECT ID_Hora, Nombre FROM horas ORDER BY ID_Hora ASC");
            while ($h = $resHoras->fetch_assoc()) {
                $nombre_h = strtolower($h['Nombre']);
                if ($nombre_h != 'recreo' && $nombre_h != 'pausa') {
                    echo "<option value='{$h['ID_Hora']}'>" . htmlspecialchars($h['Nombre']) . "</option>";
                }
            }
            ?>
        </select>

        <label for="materia">Materia:</label>
        <select id="materia">
            <option value="">Seleccione un grupo primero</option>
        </select>

        <button id="agregarBtn" type="button">Agregar Materia</button>
    </div>
</div>

<script src="lol.js"></script>
</body>
</html>