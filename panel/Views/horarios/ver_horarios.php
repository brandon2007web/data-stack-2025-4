<?php
// Incluye el archivo de conexión a la base de datos
// La variable $conn debe ser un objeto de la clase mysqli (no PDO).
include __DIR__ . "../../../../conexion.php";

// --- 1. FUNCIÓN PRINCIPAL PARA OBTENER EL HORARIO ---
function obtenerHorarioPorGrupo($conn, $idGrupo) {
    // Consulta SQL para obtener todos los detalles del horario de un grupo específico.
    $sql = "
        SELECT 
            hd.ID_Dia, 
            hd.ID_Hora, 
            s.Nombre AS DiaNombre, 
            h.Nombre AS HoraNombre, 
            a.Nombre AS AsignaturaNombre,
            g.Nombre AS GrupoNombre
        FROM horario_detalle hd
        JOIN grupo g ON hd.ID_Grupo = g.ID_Grupo
        JOIN semana s ON hd.ID_Dia = s.ID_Dia
        JOIN horas h ON hd.ID_Hora = h.ID_Hora
        JOIN asignatura a ON hd.ID_Asignatura = a.ID_Asignatura
        WHERE hd.ID_Grupo = ? 
        ORDER BY hd.ID_Dia ASC, hd.ID_Hora ASC
    ";
    
    $resultados = [];
    $grupoNombre = 'Grupo No Encontrado';

    // Usamos consultas preparadas de MySQLi para seguridad
    if (!$stmt = $conn->prepare($sql)) {
        // En caso de error en la preparación de la consulta
        die("Error al preparar la consulta de horario: " . $conn->error);
    }

    // "i" indica que el parámetro es un entero (integer)
    $stmt->bind_param("i", $idGrupo);
    $stmt->execute();
    
    // Obtiene el resultado de la consulta
    $result = $stmt->get_result(); 

    // Recorre y recupera todos los resultados usando fetch_assoc()
    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
        // Obtenemos el nombre del grupo
        if ($grupoNombre == 'Grupo No Encontrado') {
            $grupoNombre = $row['GrupoNombre'];
        }
    }
    $stmt->close();

    // Reorganizar los resultados en una matriz DÍA x HORA para fácil visualización
    $horarioEstructurado = [];
    foreach ($resultados as $row) {
        $dia = $row['DiaNombre'];
        $hora = $row['HoraNombre'];
        
        // Usamos el nombre de la hora y el nombre del día como claves
        $horarioEstructurado[$dia][$hora] = $row['AsignaturaNombre'];
    }
    
    return [
        'grupo_nombre' => $grupoNombre,
        'detalle' => $horarioEstructurado
    ];
}

// --- 2. OBTENER LISTA DE GRUPOS PARA EL SELECT (Adaptado a MySQLi) ---
$grupos = [];
if ($resultGrupos = $conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre")) {
    while ($row = $resultGrupos->fetch_assoc()) {
        $grupos[] = $row;
    }
    $resultGrupos->free();
} else {
    die("Error al cargar grupos: " . $conn->error);
}

// --- 3. PROCESAR LA SELECCIÓN DEL USUARIO (No requiere cambios) ---
$horarioData = null;
$grupoSeleccionadoID = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grupo_id'])) {
    // Usamos intval() en lugar de filter_var si ya estamos fuera de PDO
    $grupoSeleccionadoID = intval($_POST['grupo_id']); 
    if ($grupoSeleccionadoID > 0) {
        $horarioData = obtenerHorarioPorGrupo($conn, $grupoSeleccionadoID);
    }
}

// --- 4. OBTENER DÍAS Y HORAS PARA ENCABEZADOS DE LA TABLA (Adaptado a MySQLi) ---

// Obtenemos los días (Lunes a Viernes)
$dias = [];
if ($resultDias = $conn->query("SELECT Nombre FROM semana ORDER BY ID_Dia")) {
    while ($row = $resultDias->fetch_row()) { // fetch_row() es más eficiente para una sola columna
        $dias[] = $row[0];
    }
    $resultDias->free();
} else {
    die("Error al cargar días: " . $conn->error);
}

// Obtenemos las horas (1ª Hora, 2ª Hora, etc.)
$horas = [];
if ($resultHoras = $conn->query("SELECT Nombre FROM horas ORDER BY ID_Hora")) {
    while ($row = $resultHoras->fetch_row()) {
        $horas[] = $row[0];
    }
    $resultHoras->free();
} else {
    die("Error al cargar horas: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Horario por Grupo</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
       

        <?php if ($horarioData && !empty($horarioData['detalle'])): ?>
            <h2 class="titulo-grupo">Horario del Grupo: <?php echo htmlspecialchars($horarioData['grupo_nombre']); ?></h2>
            
            <table class="horario-table">
                <thead>
                    <tr>
                        <th>Hora / Día</th>
                        <?php foreach ($dias as $dia): ?>
                            <th><?php echo htmlspecialchars($dia); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horas as $hora): ?>
                        <tr>
                            <th><?php echo htmlspecialchars($hora); ?></th>
                            <?php foreach ($dias as $dia): ?>
                                <?php
                                    // Comprueba si hay una asignatura asignada para esta hora y día
                                    // Utilizamos el operador de fusión de null (??) para simplificar la lógica
                                    $asignatura = $horarioData['detalle'][$dia][$hora] ?? 'Libre'; 
                                    $clase = ($asignatura === 'Libre') ? 'vacio' : '';
                                ?>
                                <td class="<?php echo $clase; ?>">
                                    <?php echo htmlspecialchars($asignatura); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($grupoSeleccionadoID > 0): ?>
            <p>⚠️ No se encontró un horario detallado para el grupo seleccionado.</p>
        <?php endif; ?>
         <h1>Seleccionar Horario por Grupo</h1>

        <div class="select-form">
            <form method="POST">
                <label for="grupo_id">Seleccione el Grupo:</label>
                <select name="grupo_id" id="grupo_id" required>
                    <option value="">-- Elija un Grupo --</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option 
                            value="<?php echo htmlspecialchars($grupo['ID_Grupo']); ?>"
                            <?php if ($grupoSeleccionadoID == $grupo['ID_Grupo']) echo 'selected'; ?>
                        >
                            <?php echo htmlspecialchars($grupo['Nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Ver Horario</button>
            </form>
        </div>

    </div>
</body>
</html>