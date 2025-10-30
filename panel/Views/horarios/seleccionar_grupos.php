<?php
session_start();
include __DIR__ . "/../../../conexion.php";


// Si el usuario envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grupo'])) {
    $_SESSION['grupo_id'] = (int)$_POST['grupo'];
    header("Location: horarios.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Seleccionar Grupo</title>
<link rel="stylesheet" href="styles/horarios.css">
</head>
<body>

<div class="selector">
    <h2>Seleccionar Grupo</h2>
    <form method="POST">
        <label for="grupo">Grupo:</label>
        <select name="grupo" id="grupo" required>
            <option value="">-- Seleccione un grupo --</option>
            <?php
            $resGrupos = $conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre ASC");
            while ($row = $resGrupos->fetch_assoc()) {
                echo "<option value='{$row['ID_Grupo']}'>" . htmlspecialchars($row['Nombre']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Continuar</button>
    </form>
</div>

</body>
</html>
