<?php require "conexion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recursos del Aula</title>
  <style>
    body { font-family: Arial; background: #f7f7f7; padding: 20px; }
    .recurso { background: white; padding: 10px; margin: 8px 0; border-radius: 8px; }
    .disponible { color: green; font-weight: bold; }
    .no-disponible { color: red; font-weight: bold; }
    form { margin-bottom: 20px; }
  </style>
</head>
<body>
<?php
$id_aula = $_GET['id'] ?? null;
if (!$id_aula) die("Aula no especificada.");

$aula = $conn->query("SELECT Nombre FROM espacio WHERE ID_Espacio = $id_aula")->fetch_assoc();
if (!$aula) die("Aula no encontrada.");

echo "<h1>Recursos del aula: " . htmlspecialchars($aula['Nombre']) . "</h1>";

// Insertar recurso
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $cantidad = intval($_POST['cantidad']);
    $tiene_disponibilidad = isset($_POST['tiene_disponibilidad']) ? 1 : 0;
    $disponible = isset($_POST['disponible']) ? intval($_POST['disponible']) : 1;

    $conn->query("INSERT INTO recursos (Nombre, id_aula, cantidad, tiene_disponibilidad, disponible)
                  VALUES ('$nombre', $id_aula, $cantidad, $tiene_disponibilidad, $disponible)");
}

// Mostrar recursos del aula
$result = $conn->query("SELECT * FROM recursos WHERE id_aula = $id_aula");
if ($result->num_rows > 0) {
    echo "<h2>Lista de recursos</h2>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='recurso'>";
        echo "<strong>" . htmlspecialchars($row['Nombre']) . "</strong> — Cantidad: " . intval($row['cantidad']);
        if ($row['tiene_disponibilidad']) {
            $estado = $row['disponible'] ? "disponible" : "no-disponible";
            $texto = $row['disponible'] ? "Disponible" : "No disponible";
            echo " — <span class='$estado'>$texto</span>";
        }
        echo "</div>";
    }
} else {
    echo "<p>No hay recursos en esta aula.</p>";
}
?>
<!-- Formulario para agregar recurso -->
<form method="POST">
  <input type="text" name="nombre" placeholder="Nombre del recurso" required>
  <input type="number" name="cantidad" placeholder="Cantidad" min="1" value="1" required>
  <label>
    <input type="checkbox" name="tiene_disponibilidad" id="chkDisponibilidad" onchange="toggleDisponibilidad()">
    Este recurso tiene disponibilidad (por ejemplo: proyector)
  </label><br>
  <div id="estado" style="display:none;">
    <label>Estado:
      <select name="disponible">
        <option value="1">Disponible</option>
        <option value="0">No disponible</option>
      </select>
    </label>
  </div>
  <button type="submit">Agregar Recurso</button>
</form>

<script>
function toggleDisponibilidad() {
  const check = document.getElementById('chkDisponibilidad');
  document.getElementById('estado').style.display = check.checked ? 'block' : 'none';
}
</script>

<br>
<a href="aulas.php">⬅ Volver a Aulas</a>
</body>
</html>
