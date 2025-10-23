<?php
session_start();
include __DIR__ . "../../../../conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] === 'Invitado') {
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

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --bg: #f4f6fc;
    --card: #fff;
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --text: #1e1e2f;
    --muted: #6c6f85;
    --border: #e5e7eb;
    --radius: 12px;
    --hover: rgba(79,70,229,0.1);
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    margin: 0;
    color: var(--text);
    display: flex;
}

.sidebar {
    width: 280px;
    background: var(--card);
    padding: 20px;
    box-shadow: 2px 0 12px rgba(0,0,0,0.05);
    flex-shrink: 0;
    height: 100vh;
    overflow-y: auto;
    box-sizing: border-box;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 20px;
    color: var(--primary-dark);
}

.sidebar label {
    display: block;
    margin: 15px 0 5px;
    font-weight: 500;
}

.sidebar select, .sidebar input, .sidebar button {
    width: 100%;
    padding: 8px 10px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    font-size: 0.95rem;
    margin-bottom: 12px;
    box-sizing: border-box;
    outline: none;
}

.sidebar button {
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

.sidebar button:hover {
    background: var(--primary-dark);
    transform: scale(1.03);
}

.main {
    flex-grow: 1;
    padding: 30px 20px;
    overflow-x: auto;
}

h1 {
    text-align: center;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.3rem;
    color: var(--primary-dark);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: var(--card);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}

thead {
    background: var(--primary);
    color: #fff;
}

thead th {
    padding: 12px 10px;
    font-weight: 500;
}

tbody td {
    text-align: center;
    padding: 10px 8px;
    border-bottom: 1px solid var(--border);
}

tbody tr:nth-child(even) {
    background: #f9f9f9;
}

tbody tr:hover {
    background: var(--hover);
}

td.num {
    font-weight: 600;
    color: var(--primary-dark);
}

td.hora {
    font-size: 0.9rem;
    color: var(--muted);
}

td.editable {
    cursor: pointer;
    transition: background 0.2s;
}

td.editable:hover {
    background: var(--hover);
}

td.seleccionado {
    background: #ff4d4d;
    color: #fff;
    font-weight: bold;
}

</style>
</head>
<body>

<div class="sidebar">
    <h2>Agregar Materia</h2>
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

<div class="main">
    <h1>HORARIO SEMANAL</h1>
    <table id="horario">
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
            $contador_fila = 0;

            while ($row = $result->fetch_assoc()) {
                $idHora     = (int)$row['ID_Hora'];
                $nombre     = htmlspecialchars($row['Nombre']);
                $duracion   = (int)$row['Duracion'];

                $nombre_lower = strtolower($nombre);
                $es_bloque_clase = $nombre_lower != 'recreo' && $nombre_lower != 'pausa';

                $hora_inicio = date('H:i', $inicio);
                $hora_fin    = date('H:i', strtotime("+$duracion minutes", $inicio));

                if (!$es_bloque_clase) {
                    echo "<tr>
                            <td colspan='2' class='num'>{$nombre}</td>
                            <td colspan='5' class='hora'>{$hora_inicio} - {$hora_fin}</td>
                        </tr>";
                } else {
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
</div>

<script src="lol.js"></script>
</body>
</html>
