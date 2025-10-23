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
<link rel="stylesheet" href="styles/horarios.css">
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
          include(__DIR__.'/funciones/horas.php')
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
       include(__DIR__.'/funciones/func_horaas.php')
        ?>
        </tbody>
    </table>
    <button id="guardarBtn" style="display:none">Guardar Horario</button>
</div>

<script src="lol.js"></script>
</body>
</html>
