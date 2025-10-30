<?php
session_start();
include __DIR__ . "../../../../conexion.php";

// Comprobar login
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] === 'Invitado') {
    header("Location: bienvenido.php");
    exit;
}

// Comprobar si se seleccionó grupo
if (!isset($_SESSION['grupo_id'])) {
    header("Location: seleccionar_grupo.php");
    exit;
}

$grupo_id = $_SESSION['grupo_id']; // lo usás más abajo
$sql = $conn->prepare("SELECT Nombre FROM grupo WHERE ID_Grupo = ?");
$sql->bind_param("i", $grupo_id);
$sql->execute();
$result = $sql->get_result();
$nombre_grupo = "Desconocido";

if ($row = $result->fetch_assoc()) {
    $nombre_grupo = htmlspecialchars($row['Nombre']);
}
$sql->close();
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

        <label for="grupo">
    <p>Grupo seleccionado: <strong><?php echo $nombre_grupo; ?></strong></p>
</label>

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
            include(__DIR__ . '/funciones/func_horaas.php');
            // 👈 ahora se ejecuta automáticamente
            ?>

        </tbody>
    </table>
    <button id="guardarBtn" style="display:none">Guardar Horario</button>
</div>
<script>
    const GRUPO_ID = <?php echo json_encode($grupo_id); ?>;
</script>

<script src="lol.js"></script>
</body>
</html>