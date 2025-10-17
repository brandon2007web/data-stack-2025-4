<?php
// Incluye el archivo de conexión a la base de datos
// La variable $conn debe ser un objeto de la clase mysqli (no PDO).

// RUTA CORREGIDA: Se respeta la profundidad que proporcionaste, asegurando el slash.
include __DIR__ . "../../../../conexion.php";

// Definimos la hora de inicio del primer bloque.
// Puedes cambiar este valor según tu institución.
define('HORA_INICIO_CLASE_BASE', '07:00:00'); 


// --- FUNCIÓN PARA RENDERIZAR EL FORMULARIO DE SELECCIÓN DE GRUPO ---
// Esta función encapsula el HTML del formulario para poder moverlo fácilmente
function renderSelectForm($grupos, $grupoSeleccionadoID) {
    // Usamos output buffering para capturar el HTML
    ob_start();
?>
    <div class="select-form-wrapper">
        <form method="POST" class="select-form">
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
<?php
    return ob_get_clean();
}


// --- 1. FUNCIÓN PRINCIPAL PARA OBTENER EL HORARIO ---
function obtenerHorarioPorGrupo($conn, $idGrupo) {
    // Es crucial verificar que $conn no sea nulo antes de usarlo
    if (!$conn) {
        return ['grupo_nombre' => 'Error de Conexión', 'detalle' => []];
    }

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

    if (!$stmt = $conn->prepare($sql)) {
        die("Error al preparar la consulta de horario: " . $conn->error);
    }

    $stmt->bind_param("i", $idGrupo);
    $stmt->execute();
    $result = $stmt->get_result(); 

    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
        if ($grupoNombre == 'Grupo No Encontrado') {
            $grupoNombre = $row['GrupoNombre'];
        }
    }
    $stmt->close();

    // Reorganizar los resultados en una matriz DÍA x HORA, usando el Nombre de la hora
    $horarioEstructurado = [];
    foreach ($resultados as $row) {
        $dia = $row['DiaNombre'];
        $hora = $row['HoraNombre'];
        $horarioEstructurado[$dia][$hora] = $row['AsignaturaNombre'];
    }
    
    return [
        'grupo_nombre' => $grupoNombre,
        'detalle' => $horarioEstructurado
    ];
}

// --- 2. OBTENER LISTA DE GRUPOS PARA EL SELECT ---
$grupos = [];
if (isset($conn) && $resultGrupos = $conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre")) {
    while ($row = $resultGrupos->fetch_assoc()) {
        $grupos[] = $row;
    }
    $resultGrupos->free();
} elseif (!isset($conn)) {
    echo "<p style='color: red; text-align: center; font-weight: bold;'>ERROR DE CONEXIÓN: No se pudo cargar el archivo 'conexion.php'. Verifique la ruta.</p>";
} else {
    die("Error al cargar grupos: " . $conn->error);
}


// --- 3. PROCESAR LA SELECCIÓN DEL USUARIO ---
$horarioData = null;
$grupoSeleccionadoID = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grupo_id'])) {
    $grupoSeleccionadoID = intval($_POST['grupo_id']); 
    if ($grupoSeleccionadoID > 0 && isset($conn)) {
        $horarioData = obtenerHorarioPorGrupo($conn, $grupoSeleccionadoID);
    }
}

// --- 4. OBTENER DÍAS Y HORAS PARA ENCABEZADOS DE LA TABLA (CON CÁLCULO DE TIEMPO) ---
$dias = [];
$horas = [];

if (isset($conn)) {
    // Obtenemos los días (Lunes a Viernes)
    if ($resultDias = $conn->query("SELECT Nombre FROM semana ORDER BY ID_Dia")) {
        while ($row = $resultDias->fetch_row()) { 
            $dias[] = $row[0];
        }
        $resultDias->free();
    } else {
        die("Error al cargar días: " . $conn->error);
    }

    // --- LÓGICA DE CÁLCULO DE HORAS ---
    // Inicializar el timestamp con la hora base definida
    $hora_actual_timestamp = strtotime(HORA_INICIO_CLASE_BASE); 

    // Obtenemos las horas (ID_Hora, Nombre y Duracion)
    if ($resultHoras = $conn->query("SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora")) {
        while ($row = $resultHoras->fetch_assoc()) {
            
            $duracion_minutos = (int)$row['Duracion'];
            
            // Hora de inicio
            $row['HoraInicio'] = date('H:i:s', $hora_actual_timestamp);
            
            // Calcular la hora de fin sumando la duración en segundos (Duracion * 60)
            $hora_actual_timestamp += ($duracion_minutos * 60); 
            
            $row['HoraFin'] = date('H:i:s', $hora_actual_timestamp);
            
            $horas[] = $row; // Almacenamos el array completo con los nuevos campos calculados
        }
        $resultHoras->free();
    } else {
        die("Error al cargar horas: " . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Horario por Grupo</title>
    <!-- Incluimos el CSS para garantizar el diseño en el entorno Immersive -->
    <style>
        /* Estilos generales */
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            padding: 20px;
            color: #333;
            margin: 0; 
        }

        .container {
            text-align: center;
            max-width: 1100px;
            width: 100%;
        }

        /* Títulos */
        h1 {
            font-size: 28px; 
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 600;
        }

        /* Estilo de la tabla de horario */
        .horario-table {
            border-collapse: collapse;
            width: 100%;
            text-align: center;
            font-size: 14px;
            margin: 30px auto; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.15); 
            border-radius: 10px; 
            overflow: hidden;
            border: 1px solid #e0e0e0; 
        }

        .horario-table th, .horario-table td {
            border: 1px solid #e0e0e0; 
            padding: 12px 8px; 
            vertical-align: middle;
            height: 55px; 
        }

        .horario-table th {
            background: #34495e;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }

        .horario-table td {
            background: #fff;
            transition: background 0.3s;
            max-width: 200px; 
            min-width: 100px; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        .horario-table td:hover {
            background: #e9f5ff; 
        }
        
        /* Estilo para la columna de horas (header y contenido) */
        .hora-header {
            width: 120px; 
            font-weight: bold;
            background: #e9ecef; 
            color: #495057;
            position: sticky; 
            left: 0;
            z-index: 10;
            border-right: 2px solid #ccc; 
            white-space: normal; 
            line-height: 1.3; 
        }
        .hora-header small {
            font-weight: normal;
            display: block;
            color: #ffffffff; 
        }


        /* Estilos para celdas de "Libre" */
        .vacio { 
            background-color: #f8f9fa; 
            color: #99aab5; 
            font-style: italic;
            font-size: 13px; 
        }
        
        .titulo-grupo { 
            margin-top: 30px; 
            font-size: 24px; 
            color: #34495e; 
            font-weight: 600;
        }

        /* === Estilo del Formulario de Selección === */
        
        .select-form-wrapper {
            text-align: center; 
            margin-top: 25px; 
            width: 100%;
        }
        
        .select-form {
            display: inline-block; 
            padding: 20px; 
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 8px;
            width: 100%; 
            max-width: 450px; 
            text-align: left; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            transition: transform 0.2s;
            margin: 0 auto; 
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .select-form:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15); 
        }

        .select-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 15px;
            color: #333;
        }

        .select-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #ccc; 
            border-radius: 6px;
            outline: none;
            transition: border 0.3s;
            appearance: none; 
            background-color: #fff;
            /* Ícono SVG para el selector */
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%233498db%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13.2-5.4H18.6c-5%200-9.3%201.8-13.2%205.4A17.6%2017.6%200%200%200%200%2082.6c0%204.8%201.8%209.1%205.4%2013.2l128%20128c3.9%203.9%208.2%205.8%2013.2%205.8s9.3-1.9%2013.2-5.8l128-128c3.9-3.9%205.8-8.2%205.8-13.2%200-5-1.9-9.3-5.8-13.2z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 15px center; 
            background-size: 10px auto; 
        }

        .select-form select:focus {
            border: 2px solid #2980b9; 
            box-shadow: 0 0 6px rgba(52,152,219,0.4);
        }

        .select-form button {
            margin-top: 5px;
            padding: 12px 20px; 
            background: #2ecc71; 
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 6px; 
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
            width: 100%; 
        }

        .select-form button:hover {
            background: #27ae60;
        }
        
        /* Ajuste responsive para la tabla */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .horario-table {
                display: block;
                overflow-x: auto; 
                white-space: nowrap; 
            }
            .horario-table th, .horario-table td {
                padding: 10px 8px; 
                font-size: 13px;
            }
            .select-form {
                max-width: 95%;
            }
            .hora-header {
                box-shadow: 2px 0 5px rgba(0,0,0,0.1); 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Horario Semanal por Grupo</h1>

        <?php 
        // 1. Mostrar el formulario en la parte superior si NO se ha seleccionado un grupo.
        if ($grupoSeleccionadoID === null || $grupoSeleccionadoID == 0) {
            echo renderSelectForm($grupos, $grupoSeleccionadoID);
        }
        ?>

        <?php if ($horarioData && !empty($horarioData['detalle'])): ?>
            <!-- 2. Mostrar Horario (Tabla) -->
            <h2 class="titulo-grupo">Horario del Grupo: <?php echo htmlspecialchars($horarioData['grupo_nombre']); ?></h2>
            
            <table class="horario-table">
                <thead>
                    <tr>
                        <th class="hora-header">Hora / Día</th>
                        <?php foreach ($dias as $dia): ?>
                            <th><?php echo htmlspecialchars($dia); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horas as $hora): ?>
                        <tr>
                            <!-- MODIFICACIÓN: Mostrar Nombre y Rango de Hora CALCULADO -->
                            <th class="hora-header">
                                <?php echo htmlspecialchars($hora['Nombre']); ?>
                                <small>
                                    <?php 
                                        // Usamos las claves calculadas HoraInicio y HoraFin
                                        $inicio = substr($hora['HoraInicio'], 0, 5); 
                                        $fin = substr($hora['HoraFin'], 0, 5);
                                        echo "({$inicio} - {$fin})";
                                    ?>
                                </small>
                            </th>
                            <?php foreach ($dias as $dia): ?>
                                <?php
                                    // Comprueba si hay una asignatura asignada para esta hora y día
                                    $asignatura = $horarioData['detalle'][$dia][$hora['Nombre']] ?? 'Libre'; 
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

            <?php 
            // 3. Mostrar el formulario en la parte INFERIOR si ya se ha seleccionado un grupo y se muestra el horario.
            echo renderSelectForm($grupos, $grupoSeleccionadoID);
            ?>

        <?php elseif ($grupoSeleccionadoID > 0): ?>
            <p>⚠️ No se encontró un horario detallado para el grupo seleccionado.</p>
        <?php else: ?>
            <!-- Mensaje inicial, el formulario ya se mostró en el paso 1 -->
             <p>Seleccione un grupo para visualizar su horario semanal.</p>
        <?php endif; ?>

    </div>
</body>
</html>
